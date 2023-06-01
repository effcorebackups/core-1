<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class form_part {

    static protected $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (storage::get('data')->select_array('form_parts') as $c_module_id => $c_form_parts) {
                foreach ($c_form_parts as $c_row_id => $c_form_part) {
                    if (isset(static::$cache[$c_row_id])) console::report_about_duplicate('form_parts', $c_row_id, $c_module_id, static::$cache[$c_row_id]);
                              static::$cache[$c_row_id] = $c_form_part;
                              static::$cache[$c_row_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($row_id) {
        static::init();
        return static::$cache[$row_id] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

}
