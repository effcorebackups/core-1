<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use ReflectionExtension;
use stdClass;

#[\AllowDynamicProperties]

class Module_embedded {

    # ─────────────────────────────────────────────────────────────────────
    # module state diagram for modules without installation process:
    # ═════════════════════════════════════════════════════════════════════
    #
    #      ┌──────────────┐             ◯◉           ┌─────────────┐
    #      │              ├──────────────────────────▶             │
    #      │   disabled   │                          │   enabled   │
    #      │              ◀──────────────────────────┤             │
    #      └──────────────┘             ◎◯           └─────────────┘
    #
    # ─────────────────────────────────────────────────────────────────────

    # ─────────────────────────────────────────────────────────────────────
    # module state diagram for modules with installation process:
    # ═════════════════════════════════════════════════════════════════════
    #
    #      ┌────────────────────────┐   ◯◉   ┌─────────────────────┐
    #      │ uninstalled + disabled │────────▶ installed + enabled │
    #      └────────────▲───────────┘        └───────▲─────┬───────┘
    #                   │                            │     │
    #                   │ ▣ uninstall process     ◯◉ │     │ ◎◯
    #                   │                            │     │
    #      ┌────────────┴────────────────────────────┴─────▼───────┐
    #      │                 installed + disabled                  │
    #      └───────────────────────────────────────────────────────┘
    #
    # ─────────────────────────────────────────────────────────────────────

    public $id;
    public $id_bundle;
    public $title;
    public $group = 'System';
    public $description;
    public $version;
    public $copyright;
    public $path;
    public $dependencies;
    public $enabled = 'yes';
    public $icon_path;
    public $deploy_weight = +0;

    function enable() {
        if (Core::boot_insert($this->id, $this->path, 'enabled')) {
            Message::insert(
                new Text('Module "%%_title" (%%_id) was enabled.', ['title' => (new Text($this->title))->render(), 'id' => $this->id])
            );
        }
    }

    function install() {

        # ─────────────────────────────────────────────────────────────────────
        # deployment process: insert entities
        # ─────────────────────────────────────────────────────────────────────

        foreach (Entity::get_all_by_module($this->id) as $c_entity) {
            if ($c_entity->install())
                 Message::insert(new Text('Table "%%_name" was installed.'    , ['name' => $c_entity->table_name])         );
            else Message::insert(new Text('Table "%%_name" was not installed!', ['name' => $c_entity->table_name]), 'error');
        }

        # ─────────────────────────────────────────────────────────────────────
        # deployment process: check for instances duplicates
        # ─────────────────────────────────────────────────────────────────────

        $has_duplicates = false;
        foreach (Storage::get('data')->select_array('instances') as $c_module_id => $c_instances) {
            if ($c_module_id === $this->id) {
                foreach ($c_instances as $c_row_id => $c_instance) {
                    if ($c_instance->select()) {
                        $has_duplicates = true;
                        Message::insert(new Text(
                            'Duplicate of type "%%_type" with ID = "%%_id" was found in module "%%_title"!', ['type' => 'instance', 'id' => $c_row_id, 'title' => Module::get($c_instance->module_id)->title ?? 'n/a']), 'warning'
                        );
                    }
                }
            }
        }
        if ($has_duplicates) {
            Message::insert(new Text(
                'Uninstall the modules where the dependencies were found and then you can install module "%%_title".', ['title' => $this->title]), 'warning'
            );
            return;
        }

        # ─────────────────────────────────────────────────────────────────────
        # deployment process: insert instances
        # ─────────────────────────────────────────────────────────────────────

        foreach (Instance::get_all_by_module($this->id) as $c_row_id => $c_instance) {
            $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(false);
            if ($c_instance->insert())
                 Message::insert(new Text('Table row with Row ID = "%%_row_id" was appended.'    , ['row_id' => $c_row_id])         );
            else Message::insert(new Text('Table row with Row ID = "%%_row_id" was not appended!', ['row_id' => $c_row_id]), 'error');
            $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(true);
        }

        # ─────────────────────────────────────────────────────────────────────
        # deployment process: copy files
        # ─────────────────────────────────────────────────────────────────────

        $copy = Storage::get('data')->select('copy');
        if (isset($copy[$this->id]) ) {
            foreach ($copy[$this->id] as $c_info) {
                $c_src_file = new File($this->path.$c_info->from);
                $c_dst_file = new File(            $c_info->to  );
                # what to do if the file exists? to make a backup and replace, skip, replace?
                if ($c_dst_file->is_exists()) {
                    $c_if_file_exists = $c_info->if_exists ?? 'replace'; # skip | replace | backup_and_replace
                    if ($c_if_file_exists === 'replace') {} # ↓↓↓ do nothing ↓↓↓
                    if ($c_if_file_exists === 'skip') continue;
                    if ($c_if_file_exists === 'backup_and_replace') {
                        $c_dst_file_backup = clone $c_dst_file;
                        $c_dst_file_backup->name_set($c_dst_file_backup->name_get().'-'.time());
                        @rename(
                            $c_dst_file       ->path_get(),
                            $c_dst_file_backup->path_get()
                        );
                    }
                }
                # trying to copy the file
                if ($c_src_file->copy($c_dst_file->dirs_get(), $c_dst_file->file_get()))
                     Message::insert(new Text('File "%%_file" was copied to "%%_to".'    , ['file' => $c_src_file->path_get_relative(), 'to' => $c_dst_file->path_get_relative()]));
                else Message::insert(new Text('File "%%_file" was not copied to "%%_to"!', ['file' => $c_src_file->path_get_relative(), 'to' => $c_dst_file->path_get_relative()]), 'error');
            }
        }

        # ─────────────────────────────────────────────────────────────────────
        # insert to boot
        # ─────────────────────────────────────────────────────────────────────

        if (Core::boot_insert($this->id, $this->path, 'installed')) {
            Message::insert(
                new Text('Module "%%_title" (%%_id) was installed.', ['title' => (new Text($this->title))->render(), 'id' => $this->id])
            );
        }
    }

    function dependencies_info_get($scope) {
        $has_dependencies_sys = false;
        $has_dependencies_php = false;
        $dependencies_sys = isset($this->dependencies->system) && is_array($this->dependencies->system) ? $this->dependencies->system : [];
        $dependencies_php = isset($this->dependencies->php)    && is_array($this->dependencies->php)    ? $this->dependencies->php    : [];
        if ($scope === 'default') $enabled = static::get_enabled_by_default();
        if ($scope === 'boot'   ) $enabled = static::get_enabled_by_boot();
        foreach ($dependencies_sys as $c_id => $c_version_min) {
            $c_version_cur = static::get($c_id)->version;
            if (isset($enabled[$c_id]) !== true                                     ) {$dependencies_sys[$c_id] = (object)['version_min' => $c_version_min, 'state' => 0]; $has_dependencies_sys = true;}
            if (isset($enabled[$c_id]) === true && $c_version_cur  <  $c_version_min) {$dependencies_sys[$c_id] = (object)['version_min' => $c_version_min, 'state' => 1]; $has_dependencies_sys = true;}
            if (isset($enabled[$c_id]) === true && $c_version_cur === $c_version_min) {$dependencies_sys[$c_id] = (object)['version_min' => $c_version_min, 'state' => 2];}
            if (isset($enabled[$c_id]) === true && $c_version_cur  >  $c_version_min) {$dependencies_sys[$c_id] = (object)['version_min' => $c_version_min, 'state' => 3];} }
        foreach ($dependencies_php as $c_id => $c_version_min) {
            if (extension_loaded($c_id) !== true                                                                                        ) {$dependencies_php[$c_id] = (object)['version_min' => $c_version_min, 'state' => 0]; $has_dependencies_php = true;}
            if (extension_loaded($c_id) === true && version_compare((new ReflectionExtension($c_id))->getVersion(), $c_version_min, '<')) {$dependencies_php[$c_id] = (object)['version_min' => $c_version_min, 'state' => 1]; $has_dependencies_php = true;}
            if (extension_loaded($c_id) === true && version_compare((new ReflectionExtension($c_id))->getVersion(), $c_version_min, '=')) {$dependencies_php[$c_id] = (object)['version_min' => $c_version_min, 'state' => 2];}
            if (extension_loaded($c_id) === true && version_compare((new ReflectionExtension($c_id))->getVersion(), $c_version_min, '>')) {$dependencies_php[$c_id] = (object)['version_min' => $c_version_min, 'state' => 3];} }
        $result = new stdClass;
        $result->has_dependencies_sys = $has_dependencies_sys;
        $result->has_dependencies_php = $has_dependencies_php;
        $result->sys = $dependencies_sys;
        $result->php = $dependencies_php;
        return $result;
    }

    function required_for_info_get($scope) {
        $has_required = false;
        $required = [];
        if ($scope === 'default') $enabled = static::get_enabled_by_default();
        if ($scope === 'boot'   ) $enabled = static::get_enabled_by_boot();
        foreach (static::get_all() as $c_module) {
            if (isset($c_module->dependencies->system[$this->id]) === true && isset($enabled[$c_module->id]) !== true) {$required[$c_module->id] = (object)['state' => 0];}
            if (isset($c_module->dependencies->system[$this->id]) === true && isset($enabled[$c_module->id]) === true) {$required[$c_module->id] = (object)['state' => 1]; $has_required = true;} }
        $result = new stdClass;
        $result->has_required = $has_required;
        $result->req = $required;
        return $result;
    }

    function group_get_id() {
        return Security::sanitize_id($this->group);
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            static::$cache['modules'] = Storage::get('data')->select('module');
            static::$cache['bundles'] = Storage::get('data')->select('bundle');
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function is_enabled($module_id) {
        $enabled = Core::boot_select('enabled');
        return isset($enabled[$module_id]);
    }

    static function is_installed($module_id) {
        $installed = Core::boot_select('installed');
        return isset($installed[$module_id]);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function get($id) {
        static::init();
        return static::$cache['modules'][$id] ?? null;
    }

    static function get_all($property = null) {
        static::init();
        $result = [];
        foreach (static::$cache['modules'] as $c_module)
            $result[$c_module->id] = $property ?
                    $c_module->{$property} :
                    $c_module;
        return $result;
    }

    static function get_profiles($property = null, $with_disabled_by_default = false) {
        $result = [];
        foreach (static::get_all() as $c_module) {
            if ($c_module instanceof Module_as_profile) {
                if ($c_module->enabled !== 'yes' && $with_disabled_by_default === true) $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
                if ($c_module->enabled === 'yes'                                      ) $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
            }
        }
        return $result;
    }

    static function get_embedded($property = null) {
        $result = [];
        foreach (static::get_all() as $c_module)
            if ($c_module instanceof Module_embedded &&
               !$c_module instanceof Module)
                $result[$c_module->id] = $property ?
                        $c_module->{$property} :
                        $c_module;
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function get_enabled_by_boot() {
        return Core::boot_select('enabled');
    }

    static function get_enabled_by_default($property = null) {
        $result = [];
        foreach (static::get_all() as $c_module)
            if ($c_module->enabled === 'yes')
                $result[$c_module->id] = $property ?
                        $c_module->{$property} :
                        $c_module;
        return $result;
    }

    static function get_installed($with_enabled = true, $with_disabled = true) {
        $result    = [];
        $installed = Core::boot_select('installed');
        $enabled   = Core::boot_select('enabled');
        foreach ($installed as $c_id => $c_path) {
            if ($with_enabled  === true && isset($enabled[$c_id]) === true) $result[$c_id] = $c_path;
            if ($with_disabled === true && isset($enabled[$c_id]) !== true) $result[$c_id] = $c_path;
        }
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function bundle_get($id) {
        static::init();
        return static::$cache['bundles'][$id] ?? null;
    }

    static function bundle_get_all($property = null) {
        static::init();
        $result = [];
        foreach (static::$cache['bundles'] as $c_bundle)
            $result[$c_bundle->id] = $property ?
                    $c_bundle->{$property} :
                    $c_bundle;
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function groups_get() {
        static::init();
        $groups = [];
        foreach (static::$cache['modules'] as $c_module)
            $groups[Security::sanitize_id($c_module->group)] = $c_module->group;
        return $groups;
    }

    static function settings_get($module_id) {
        $settings = Storage::get('data')->select_array('settings');
        return $settings[$module_id] ?? [];
    }

}
