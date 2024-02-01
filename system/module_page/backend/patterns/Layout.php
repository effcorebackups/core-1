<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Layout extends Node implements has_Data_cache {

    public $id;
    public $title;
    public $is_manageable = false;
    public $states = [];

    function render_self() {
        return '';
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function not_external_properties_get() {
        return [
            'id' => 'id'
        ];
    }

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('layouts') as $c_module_id => $c_layouts) {
                foreach ($c_layouts as $c_id => $c_layout) {
                    if (isset(static::$cache[$c_id])) Console::report_about_duplicate('layouts', $c_id, $c_module_id, static::$cache[$c_id]);
                              static::$cache[$c_id] = $c_layout;
                              static::$cache[$c_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function select_all($load = true) {
        static::init();
        if ($load)
            foreach (static::$cache as $id => $c_item)
                if (static::$cache[$id] instanceof External_cache)
                    static::$cache[$id] =
                    static::$cache[$id]->load_from_nosql_storage();
        return static::$cache;
    }

    static function select($id, $load = true) {
        static::init();
        if (isset(static::$cache[$id]) === false) return;
        if (static::$cache[$id] instanceof External_cache && $load)
               static::$cache[$id] =
               static::$cache[$id]->load_from_nosql_storage();
        return static::$cache[$id];
    }

    static function changes_store($module_id, $layout_id, $states = []) {
        $result = true;
        foreach ($states as $c_state) {
            if ($c_state !== null) $result&= Storage::get('data')->changes_register  ($module_id, 'update', 'layouts/'.$module_id.'/'.$layout_id.'/states', $states, false);
            if ($c_state === null) $result&= Storage::get('data')->changes_unregister($module_id, 'update', 'layouts/'.$module_id.'/'.$layout_id.'/states', null   , false);
        }
        $result&= Storage_Data::cache_update();
        return $result;
    }

}
