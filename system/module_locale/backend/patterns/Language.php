<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Language {

    public $code;
    public $title_en;
    public $title_native;
    public $license_path;

    function formats_get() {
        $settings = Module::settings_get('locale');
        return $settings->formats[$this->code] ??
                                  $this->default_formats;
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $current;
    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('languages') as $c_module_id => $c_languages) {
                foreach ($c_languages as $c_row_id => $c_language) {
                    if (isset(static::$cache[$c_language->code])) Console::report_about_duplicate('languages', $c_language->code, $c_module_id, static::$cache[$c_language->code]);
                              static::$cache[$c_language->code] = $c_language;
                              static::$cache[$c_language->code]->module_id = $c_module_id;
                }
            }
            foreach (Storage::get('data')->select_array('plurals') as $c_module_id => $c_plurals_by_module) {
                foreach ($c_plurals_by_module as $c_plurals_by_language) {
                    foreach ($c_plurals_by_language->data as $c_plural_name => $c_plural_info) {
                        if (isset(static::$cache[$c_plurals_by_language->code]))
                                  static::$cache[$c_plurals_by_language->code]->plurals[$c_plural_name] = $c_plural_info;
                    }
                }
            }
        }
    }

    static function get($code) {
        static::init();
        return static::$cache[$code] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

    static function plurals_get($code) {
        return static::get($code)->plurals ?? [];
    }

    static function code_get_from_settings() {
        return Module::settings_get('locale')->lang_code;
    }

    static function code_get_current() {
        if   (!static::$current)
               static::$current = static::code_get_from_settings();
        return static::$current;
    }

    static function code_set_current($code) {
        static::$current = $code;
    }

}
