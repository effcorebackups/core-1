<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module_embed {

  # module state diagram for modules without installation process
  # ─────────────────────────────────────────────────────────────────────
  #
  #   ┌──────────────┐             ◯◉           ┌─────────────┐
  #   │              ├──────────────────────────▶             │
  #   │   disabled   │                          │   enabled   │
  #   │              ◀──────────────────────────┤             │
  #   └──────────────┘             ◎◯           └─────────────┘
  #

  # module state diagram for modules with installation process
  # ─────────────────────────────────────────────────────────────────────
  #
  #   ┌────────────────────────┐   ◯◉   ┌─────────────────────┐
  #   │ uninstalled + disabled │────────▶ installed + enabled │
  #   └────────────▲───────────┘        └───────▲─────┬───────┘
  #                │                            │     │
  #                │ ▣ uninstall process     ◯◉ │     │ ◎◯
  #                │                            │     │
  #   ┌────────────┴────────────────────────────┴─────▼───────┐
  #   │                 installed + disabled                  │
  #   └───────────────────────────────────────────────────────┘
  #

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
  public $deploy_weight = 0;

  function enable() {
    core::boot_insert($this->id, $this->path, 'enabled');
    message::insert(
      new text('Module "%%_title" (%%_id) was enabled.', ['title' => translation::get($this->title), 'id' => $this->id])
    );
  }

  function install() {
  # insert entities
    foreach (entity::get_all_by_module($this->id) as $c_entity) {
      if ($c_entity->install())
           message::insert(new text('Entity "%%_name" was installed.',     ['name' => $c_entity->name])         );
      else message::insert(new text('Entity "%%_name" was not installed!', ['name' => $c_entity->name]), 'error');
    }
  # insert instances
    foreach (instance::get_all_by_module($this->id) as $c_row_id => $c_instance) {
      $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(0);
      if ($c_instance->insert()) message::insert(new text('Instance with row_id = "%%_row_id" was inserted.',     ['row_id' => $c_row_id])         );
      else                       message::insert(new text('Instance with row_id = "%%_row_id" was not inserted!', ['row_id' => $c_row_id]), 'error');
      $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(1);
    }
  # insert to boot
    core::boot_insert($this->id, $this->path, 'installed');
    message::insert(
      new text('Module "%%_title" (%%_id) was installed.', ['title' => translation::get($this->title), 'id' => $this->id])
    );
  }

  function dependencies_status_get() {
    $dependencies_php = $this->dependencies->php    ?? [];
    $dependencies_sys = $this->dependencies->system ?? [];
    $boot_status = core::boot_select();
    foreach ($dependencies_php as $c_id => $c_version_min) $dependencies_php[$c_id] = (int)(extension_loaded  ($c_id)  && version_compare((new \ReflectionExtension($c_id))->getVersion(), $c_version_min, '>='));
    foreach ($dependencies_sys as $c_id => $c_version_min) $dependencies_sys[$c_id] = (int)(isset($boot_status[$c_id]) &&                               static::get($c_id)->version   >=   $c_version_min       );
    return (object)[
      'php' => $dependencies_php,
      'sys' => $dependencies_sys
    ];
  }

  function depended_status_get() {
    $result = [];
    $boot_status = core::boot_select();
    foreach (static::get_all() as $c_module) {
      $c_dependencies_sys = $c_module->dependencies->system ?? [];
      if (isset($c_dependencies_sys[$this->id])) {
        $result[$c_module->id] = (int)isset($boot_status[$c_module->id]);
      }
    }
    return $result;
  }

  function group_get_id() {
    return core::sanitize_id($this->group);
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      static::$cache['modules'] = storage::get('files')->select('module');
      static::$cache['bundles'] = storage::get('files')->select('bundle');
    }
  }

  static function get($id) {
    static::init();
    return static::$cache['modules'][$id];
  }

  static function get_all($property = null) {
    static::init();
    $result = [];
    foreach (static::$cache['modules'] as $c_module) {
      $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
    }
    return $result;
  }

  static function get_embed($property = null) {
    $result = [];
    foreach (static::get_all() as $c_module) {
      if ($c_module instanceof module_embed &&
         !$c_module instanceof module) {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function get_enabled_by_default($property = null) {
    $result = [];
    foreach (static::get_all() as $c_module) {
      if ($c_module->enabled == 'yes') {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function get_enabled() {
    return core::boot_select('enabled');
  }

  static function get_installed($ws_enabled = true, $ws_disabled = true) {
    $result    = [];
    $installed = core::boot_select('installed');
    $enabled   = core::boot_select('enabled'  );
    foreach ($installed as $c_id => $c_path) {
      if ($ws_enabled  == true && isset($enabled[$c_id]) == true) $result[$c_id] = $c_path;
      if ($ws_disabled == true && isset($enabled[$c_id]) != true) $result[$c_id] = $c_path;
    }
    return $result;
  }

  static function is_enabled($module_id) {
    $enabled = core::boot_select('enabled');
    return isset($enabled[$module_id]);
  }

  static function is_installed($module_id) {
    $installed = core::boot_select('installed');
    return isset($installed[$module_id]);
  }

  static function is_required_updates() {
    foreach (static::get_all() as $c_module) {
      $c_updates            = static::updates_get           ($c_module->id);
      $c_update_last_number = static::update_get_last_number($c_module->id);
      foreach ($c_updates as $c_update) {
        if ($c_update->number > $c_update_last_number) return true;
      }
    }
  }

  static function update_get_last_number($module_id) {
    $settings = static::settings_get($module_id);
    return $settings->update_last_number ?? 0;
  }

  static function updates_get($module_id, $from_number = 0) {
    $updates = [];
    foreach (storage::get('files')->select('module_updates', false, false) ?? [] as $c_module_id => $c_updates)
      if ($c_module_id == $module_id)
        foreach ($c_updates as $c_row_id => $c_update)
          if ($c_update->number >= $from_number)
            $updates[$c_row_id] = $c_update;
    return $updates;
  }

  static function settings_get($module_id) {
    $settings = storage::get('files')->select('settings');
    return $settings[$module_id] ?? [];
  }

  static function bundle_get($id) {
    static::init();
    return static::$cache['bundles'][$id];
  }

  static function groups_get() {
    static::init();
    $groups = [];
    foreach (static::$cache['modules'] as $c_module)
      $groups[core::sanitize_id($c_module->group)] = $c_module->group;
    return $groups;
  }

}}