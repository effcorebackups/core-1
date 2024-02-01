<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Entity implements has_Data_cache, has_postparse, cache_cleaning_after_install {

    public $name;
    public $storage_name = 'sql';
    public $table_name;
    public $fields      = [];
    public $constraints = [];
    public $indexes     = [];

    public $has_parallel_checking               = false;
    public $has_relation_checking               = false;
    public $has_button_insert_and_update        = false;
    public $has_message_for_additional_controls = false;

    public $with_is_embedded = false;
    public $with_module_id   = false;
    public $with_origin      = false;
    public $with_data        = false;

    public $title;
    public $title_plural;
    public $managing_is_enabled = false;
    public $managing_group_id = 'content';

    public $access;

    function _postparse() {
        if ($this->access === null) $this->access = new stdClass;
        if ($this->managing_is_enabled && empty($this->access->on_select)) $this->access->on_select = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && empty($this->access->on_insert)) $this->access->on_insert = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && empty($this->access->on_update)) $this->access->on_update = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && empty($this->access->on_delete)) $this->access->on_delete = (object)['roles' => ['admins' => 'admins']];
        # insert field 'is_embedded'
        if ($this->with_is_embedded) {
            $this->fields['is_embedded'] = new stdClass;
            $this->fields['is_embedded']->title = 'Is embedded';
            $this->fields['is_embedded']->type = 'boolean';
            $this->fields['is_embedded']->not_null = true;
            $this->fields['is_embedded']->default = 0;
            $this->fields['is_embedded']->managing = new stdClass;
            $this->fields['is_embedded']->managing->control = new stdClass;
            $this->fields['is_embedded']->managing->control->class = '\\effcore\\Field_Switcher';
            $this->fields['is_embedded']->managing->control->properties['weight'] = 390;
            $this->fields['is_embedded']->managing->control->element_attributes['disabled'] = true;
        }
        # insert field 'module_id' and index for it
        if ($this->with_module_id) {
            $this->fields['module_id'] = new stdClass;
            $this->fields['module_id']->title = 'Module ID';
            $this->fields['module_id']->type = 'varchar';
            $this->fields['module_id']->size = 64;
            $this->fields['module_id']->collate = 'nocase';
            $this->fields['module_id']->default = null;
            $this->fields['module_id']->check = '(module_id <> \'\')';
            $this->indexes['index_module_id'] = new stdClass;
            $this->indexes['index_module_id']->type = 'index';
            $this->indexes['index_module_id']->fields = ['module_id' => 'module_id'];
        }
        # insert field 'origin' and index for it
        if ($this->with_origin) {
            $this->fields['origin'] = new stdClass;
            $this->fields['origin']->title = 'Origin';
            $this->fields['origin']->type = 'varchar';
            $this->fields['origin']->size = 16;
            $this->fields['origin']->collate = 'nocase';
            $this->fields['origin']->default = 'sql';
            $this->fields['origin']->check = '(origin <> \'\')';
            $this->indexes['index_origin'] = new stdClass;
            $this->indexes['index_origin']->type = 'index';
            $this->indexes['index_origin']->fields = ['origin' => 'origin'];
        }
        # insert field 'data'
        if ($this->with_data) {
            $this->fields['data'] = new stdClass;
            $this->fields['data']->title = 'Data';
            $this->fields['data']->type = 'blob';
            $this->fields['data']->converters = new stdClass;
            $this->fields['data']->converters->on_select = 'unserialize';
            $this->fields['data']->converters->on_insert = '\\effcore\\Core::data_serialize';
            $this->fields['data']->converters->on_update = '\\effcore\\Core::data_serialize';
            $this->fields['data']->converters->on_render = '\\effcore\\Core::data_serialize';
        }
    }

    function storage_get() {
        return Storage::get($this->storage_name);
    }

    function field_get($name) {
        return $this->fields[$name] ?? null;
    }

    function fields_get_name() {
        return Core::array_keys_map(
            array_keys($this->fields)
        );
    }

    function fields_get_title() {
        $result = [];
        foreach ($this->fields as $name => $info)
            $result[$name] = $info->title ?? null;
        return $result;
    }

    function auto_name_get() {
        foreach ($this->fields as $name => $info) {
            if ($info->type === 'autoincrement') {
                return $name;
            }
        }
    }

    function references_get() {
        $result = [];
        foreach ($this->constraints as $c_row_id => $c_constraint)
            if ($c_constraint->type === 'foreign')
                $result[$c_row_id] = $c_constraint;
        return $result;
    }

    function references_to_me_get() {
        $result = [];
        foreach (static::get_all() as $c_entity) {
            if ($c_entity->name !== $this->name) {
                foreach ($c_entity->constraints as $c_row_id => $c_constraint) {
                    if ($c_constraint->type === 'foreign')
                        if ($c_constraint->reference_entity === $this->name)
                            $result[$c_entity->name][$c_row_id] = $c_constraint; }}}
        return $result;
    }

    function id_get() {
        foreach ($this->constraints as $c_constraint) if ($c_constraint->type === 'primary'     ) return $c_constraint->fields;
        foreach ($this->constraints as $c_constraint) if ($c_constraint->type === 'unique'      ) return $c_constraint->fields;
        foreach ($this->indexes     as $c_index     ) if ($c_index     ->type === 'unique index') return $c_index     ->fields;
        return [];
    }

    function id_from_values_get($values) {
        foreach ($this->constraints as $c_constraint) if ($c_constraint->type === 'primary'     ) {$slice = []; foreach ($c_constraint->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_constraint->fields) === count($slice)) return $slice;}
        foreach ($this->constraints as $c_constraint) if ($c_constraint->type === 'unique'      ) {$slice = []; foreach ($c_constraint->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_constraint->fields) === count($slice)) return $slice;}
        foreach ($this->indexes     as $c_index     ) if ($c_index     ->type === 'unique index') {$slice = []; foreach ($c_index     ->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_index     ->fields) === count($slice)) return $slice;}
        return [];
    }

    function install() {
        return $this->storage_get()->entity_install($this);
    }

    function uninstall() {
        return $this->storage_get()->entity_uninstall($this);
    }

    function truncate() {
        return $this->storage_get()->entity_truncate($this);
    }

    function instances_select_count($params = []) {
        return $this->storage_get()->instances_select_count($this, $params);
    }

    function instances_select($params = [], $idkey = null) {
        return $this->storage_get()->instances_select($this, $params, $idkey);
    }

    function instances_delete($params = []) {
        return $this->storage_get()->instances_delete($this, $params);
    }

    function make_url_for_select_multiple() {return '/manage/data/'.$this->managing_group_id.'/'.$this->name;           }
    function make_url_for_insert         () {return '/manage/data/'.$this->managing_group_id.'/'.$this->name.'//insert';}

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;
    protected static $cache_orig;

    static function not_external_properties_get() {
        return [
            'name'              => 'name',
            'storage_name'      => 'storage_name',
            'table_name'        => 'table_name',
            'title'             => 'title',
            'title_plural'      => 'title_plural',
            'managing_group_id' => 'managing_group_id'
        ];
    }

    static function cache_cleaning() {
        static::$cache      = null;
        static::$cache_orig = null;
    }

    static function init() {
        if (static::$cache === null) {
            static::$cache_orig = Storage::get('data')->select_array('entities');
            foreach (static::$cache_orig as $c_module_id => $c_entities) {
                foreach ($c_entities as $c_row_id => $c_entity) {
                    if (isset(static::$cache[$c_entity->name])) Console::report_about_duplicate('entities', $c_entity->name, $c_module_id, static::$cache[$c_entity->name]);
                              static::$cache[$c_entity->name] = $c_entity;
                              static::$cache[$c_entity->name]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($name, $load = true) {
        static::init();
        if (isset(static::$cache[$name]) === false) return;
        if (static::$cache[$name] instanceof External_cache && $load)
            static::$cache[$name] =
            static::$cache[$name]->load_from_nosql_storage();
        return static::$cache[$name];
    }

    static function get_all($load = true) {
        static::init();
        if ($load)
            foreach (static::$cache as $c_name => $c_item)
                if (static::$cache[$c_name] instanceof External_cache)
                    static::$cache[$c_name] =
                    static::$cache[$c_name]->load_from_nosql_storage();
        return static::$cache;
    }

    static function get_all_by_module($module, $load = true) {
        static::init();
        if ($load && isset(static::$cache_orig[$module]))
            foreach (static::$cache_orig[$module] as $c_name => $c_item)
                if (static::$cache_orig[$module][$c_name] instanceof External_cache)
                    static::$cache_orig[$module][$c_name] =
                    static::$cache_orig[$module][$c_name]->load_from_nosql_storage();
        return static::$cache_orig[$module] ?? [];
    }

    static function get_managing_group_ids() {
        static::init();
        $groups = [];
        foreach (static::$cache as $c_item)
            $groups[$c_item->managing_group_id] = $c_item->managing_group_id;
        return $groups;
    }

    static function converters_apply($value, $converters = []) {
        if (!is_array($converters))
                      $converters = [$converters];
        krsort($converters, SORT_NUMERIC);
        foreach ($converters as $c_converter) {
            if (Core::is_handler($c_converter) === true && !Core::handler_exists($c_converter)) throw new Extend_exception('Converter is not available! Converter: "'.$c_converter.'"', 0, 'Converter "%%_name" is not available!', ['name' => $c_converter]);
            if (Core::is_handler($c_converter) !== true &&      !function_exists($c_converter)) throw new Extend_exception('Converter is not available! Converter: "'.$c_converter.'"', 0, 'Converter "%%_name" is not available!', ['name' => $c_converter]);
            $value = call_user_func($c_converter, $value);
        }
        return $value;
    }

}
