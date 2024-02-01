<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Template {

    public $name;
    public $data;
    public $args = [];
    public $args_raw = [];
    public $module_id;

    function __construct($name, $args = []) {
        $this->name = $name;
        foreach ($args as $c_key => $c_value) {
            $this->arg_set($c_key, $c_value);
        }
    }

    function arg_set($name, $value, $value_raw = null) {
        $this->args    [$name] = $value;
        $this->args_raw[$name] = $value_raw;
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
            foreach (Storage::get('data')->select_array('templates') as $c_module_id => $c_templates) {
                foreach ($c_templates as $c_row_id => $c_template) {
                    if (isset(static::$cache[$c_template->name])) Console::report_about_duplicate('templates', $c_template->name, $c_module_id, static::$cache[$c_template->name]);
                              static::$cache[$c_template->name] = $c_template;
                              static::$cache[$c_template->name]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($name) {
        static::init();
        return static::$cache[$name] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

    static function copied_properties_get() {
        return [
            'module_id' => 'module_id',
            'data'      => 'data'
        ];
    }

    static function pick_name($name) {
        static::init();
        if (isset(static::$cache[$name             ])) return $name;
        if (isset(static::$cache[$name.'__embedded'])) return $name.'__embedded';
        return null;
    }

    static function make_new($name, $args = []) {
        $template = static::get($name);
        $class_name = '\\effcore\\Template_'.$template->type;
        $result = new $class_name($name, $args);
        foreach ($class_name::copied_properties_get() as $c_property_name) {
            if (property_exists($template, $c_property_name)) {
                $result->{$c_property_name} = $template->{$c_property_name};
            }
        }
        return $result;
    }

}
