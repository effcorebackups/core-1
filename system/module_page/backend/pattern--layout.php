<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class layout extends node implements has_external_cache {

    public $id;
    public $title;

    function render_self() {
        return '';
    }

    ###########################
    ### static declarations ###
    ###########################

    static protected $cache;

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
            foreach (storage::get('data')->select_array('layouts') as $c_module_id => $c_layouts) {
                foreach ($c_layouts as $c_id => $c_layout) {
                    if (isset(static::$cache[$c_id])) console::report_about_duplicate('layouts', $c_id, $c_module_id, static::$cache[$c_id]);
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
                if (static::$cache[$id] instanceof external_cache)
                    static::$cache[$id] =
                    static::$cache[$id]->load_from_nosql_storage();
        return static::$cache;
    }

    static function select($id, $load = true) {
        static::init();
        if (isset(static::$cache[$id]) === false) return;
        if (static::$cache[$id] instanceof external_cache && $load)
               static::$cache[$id] =
               static::$cache[$id]->load_from_nosql_storage();
        return static::$cache[$id];
    }

}
