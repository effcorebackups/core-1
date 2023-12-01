<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test implements has_Data_cache {

    public $id;
    public $title;
    public $description;
    public $type = 'php';
    public $params;
    public $scenario;

    function run() {
        $c_results = [];
        foreach ($this->scenario as $c_dpath => $c_step) {
            $c_step->run($this, $c_dpath, $c_results);
            if (array_key_exists('return', $c_results)) {
                break;
            }
        }
        return $c_results;
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function not_external_properties_get() {
        return [
            'id'    => 'id',
            'title' => 'title'
        ];
    }

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('tests') as $c_module_id => $c_tests) {
                foreach ($c_tests as $c_row_id => $c_test) {
                    if (isset(static::$cache[$c_test->id])) Console::report_about_duplicate('tests', $c_test->id, $c_module_id, static::$cache[$c_test->id]);
                              static::$cache[$c_test->id] = $c_test;
                              static::$cache[$c_test->id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($id, $load = true) {
        static::init();
        if (isset(static::$cache[$id]) === false) return;
        if (static::$cache[$id] instanceof External_cache && $load)
            static::$cache[$id] =
            static::$cache[$id]->load_from_nosql_storage();
        return static::$cache[$id];
    }

    static function get_all($load = true) {
        static::init();
        if ($load)
            foreach (static::$cache as $id => $c_item)
                if (static::$cache[$id] instanceof External_cache)
                    static::$cache[$id] =
                    static::$cache[$id]->load_from_nosql_storage();
        return static::$cache;
    }

    static function result_prepare($value, $depth = 0) {
        $result = '';
        switch (gettype($value)) {
            case 'string' : $result =         $value;                    break;
            case 'integer': $result = (string)$value;                    break;
            case 'double' : $result = (string)$value;                    break;
            case 'boolean': $result =         $value ? 'true' : 'false'; break;
            case 'NULL'   : $result =                  'null';           break;
            case 'array'  :
            case 'object' :
                $lines = $depth === 0 ? ['⌖|'.gettype($value)] : [];
                $list_symbol = gettype($value) === 'array' ? '- ' : '  ';
                foreach ($value as $c_key => $c_value)
                    if (is_object($c_value) || is_array($c_value)) {
                        if (!count((array)$c_value))
                             $lines[] = str_repeat('  ', $depth).$list_symbol.$c_key.'|'.gettype($c_value);
                        else $lines[] = str_repeat('  ', $depth).$list_symbol.$c_key.'|'.gettype($c_value).NL.static::result_prepare($c_value, $depth + 1); }
                    else     $lines[] = str_repeat('  ', $depth).$list_symbol.$c_key.': '.                    static::result_prepare($c_value, $depth + 1);
                $result = implode(NL, $lines);
                break;
        }
        if (!Core::is_CLI() && $depth === 0) {
            $result = str_replace(['<', '>'], ['&lt;', '&gt;'], $result);
        }
        if (str_contains($result, NL) && $depth === 0) {
            $result = NL.$result;
        }
        return $result;
    }

}
