<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

class entity implements has_external_cache, should_clear_cache_after_on_install, has_postparse {

    public $name;
    public $storage_name = 'sql';
    public $catalog_name;
    public $fields      = [];
    public $constraints = [];
    public $indexes     = [];

    public $has_parallel_checking               = false;
    public $has_button_insert_and_update        = false;
    public $has_message_for_additional_controls = false;

    public $ws_is_embedded = false;
    public $ws_module_id   = false;
    public $ws_origin      = false;
    public $ws_data        = false;

    public $title;
    public $title_plural;
    public $managing_is_enabled = false;
    public $managing_group_id = 'content';

    public $access_select;
    public $access_insert;
    public $access_update;
    public $access_delete;

    function _postparse() {
        if ($this->managing_is_enabled && $this->access_select === null) $this->access_select = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && $this->access_insert === null) $this->access_insert = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && $this->access_update === null) $this->access_update = (object)['roles' => ['admins' => 'admins']];
        if ($this->managing_is_enabled && $this->access_delete === null) $this->access_delete = (object)['roles' => ['admins' => 'admins']];
        # insert field 'is_embedded'
        if ($this->ws_is_embedded) {
            $this->fields['is_embedded'] = new stdClass;
            $this->fields['is_embedded']->title = 'Is embedded';
            $this->fields['is_embedded']->type = 'boolean';
            $this->fields['is_embedded']->not_null = true;
            $this->fields['is_embedded']->default = 0;
            $this->fields['is_embedded']->managing_control_class = '\\effcore\\field_switcher';
            $this->fields['is_embedded']->managing_control_properties['weight'] = 390;
            $this->fields['is_embedded']->managing_control_element_attributes['disabled'] = true;
        }
        # insert field 'module_id' and index for it
        if ($this->ws_module_id) {
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
        if ($this->ws_origin) {
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
        if ($this->ws_data) {
            $this->fields['data'] = new stdClass;
            $this->fields['data']->title = 'Data';
            $this->fields['data']->type = 'blob';
            $this->fields['data']->converter_on_select = 'unserialize';
            $this->fields['data']->converter_on_insert = '\\effcore\\core::data_serialize';
            $this->fields['data']->converter_on_update = '\\effcore\\core::data_serialize';
        }
    }

    function storage_get() {
        return storage::get($this->storage_name);
    }

    function field_get($name) {
        return $this->fields[$name] ?? null;
    }

    function fields_get_name() {
        return core::array_keys_map(
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

    static protected $cache;
    static protected $cache_orig;

    static function not_external_properties_get() {
        return [
            'name'              => 'name',
            'storage_name'      => 'storage_name',
            'catalog_name'      => 'catalog_name',
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
            static::$cache_orig = storage::get('data')->select_array('entities');
            foreach (static::$cache_orig as $c_module_id => $c_entities) {
                foreach ($c_entities as $c_row_id => $c_entity) {
                    if (isset(static::$cache[$c_entity->name])) console::report_about_duplicate('entities', $c_entity->name, $c_module_id, static::$cache[$c_entity->name]);
                              static::$cache[$c_entity->name] = $c_entity;
                              static::$cache[$c_entity->name]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($name, $load = true) {
        static::init();
        if (isset(static::$cache[$name]) === false) return;
        if (static::$cache[$name] instanceof external_cache && $load)
            static::$cache[$name] =
            static::$cache[$name]->load_from_nosql_storage();
        return static::$cache[$name];
    }

    static function get_all($load = true) {
        static::init();
        if ($load)
            foreach (static::$cache as $c_name => $c_item)
                if (static::$cache[$c_name] instanceof external_cache)
                    static::$cache[$c_name] =
                    static::$cache[$c_name]->load_from_nosql_storage();
        return static::$cache;
    }

    static function get_all_by_module($module, $load = true) {
        static::init();
        if ($load && isset(static::$cache_orig[$module]))
            foreach (static::$cache_orig[$module] as $c_name => $c_item)
                if (static::$cache_orig[$module][$c_name] instanceof external_cache)
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

}
