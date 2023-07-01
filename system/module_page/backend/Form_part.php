<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Form_part {

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('form_parts') as $c_module_id => $c_form_parts) {
                foreach ($c_form_parts as $c_row_id => $c_form_part) {
                    if (isset(static::$cache[$c_row_id])) Console::report_about_duplicate('form_parts', $c_row_id, $c_module_id, static::$cache[$c_row_id]);
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
