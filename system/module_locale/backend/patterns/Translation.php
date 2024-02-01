<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Translation implements has_Data_cache {

    public $code;
    public $data;

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function not_external_properties_get() {
        return [
            'code' => 'code'
        ];
    }

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init($code) {
        if (!isset(static::$cache[$code])) {
            foreach (Storage::get('data')->select_array('translations') as $c_module_id => $c_translations) {
                foreach ($c_translations as $c_row_id => $c_translation) {
                    if ($c_translation->code === $code) {
                        if ($c_translation instanceof External_cache)
                            $c_translation =
                            $c_translation->load_from_nosql_storage();
                        if (!isset(static::$cache[$c_translation->code]))
                                   static::$cache[$c_translation->code] = [];
                        static::$cache[$c_translation->code] += $c_translation->data;
                    }
                }
            }
        }
    }

    static function select_all_by_code($code = '') {
        $c_code = $code ?: Language::code_get_current();
        if ($c_code !== 'en') static::init($c_code);
        return static::$cache[$c_code] ?? [];
    }

    static function apply($string, $args = [], $code = '') {
        $c_code = $code ?: Language::code_get_current();
        if ($c_code !== 'en') static::init($c_code);
        $string = static::$cache[$c_code][$string] ?? $string;
        if ($string) {
            return preg_replace_callback('%\\%\\%_'.'(?<name>[a-z0-9_]{1,64})'.
                                           '(?:\\('.'(?<args>.{1,1024}?)'.'(?<!\\\\)'.'\\)|)%S', function ($c_match) use ($c_code, $args) {
                $c_name =       $c_match['name'];
                $c_args = isset($c_match['args']) ? explode('|', $c_match['args']) : [];
                # plurals functionality
                if ($c_name === 'plural') {
                    $c_result = static::plural($c_args, $args, $c_code);
                    if (is_string($c_result)) {
                        return $c_result;
                    }
                }
                # default case
                return $args[$c_name] ?? $c_match[0];
            }, $string);
        } else {
            return $string;
        }
    }

    static function plural($args = [], $transl_args = [], $code = 'en') { # @return: null | '' | 'string'
        if (isset($args[0]) &&
            isset($args[1])) {
            $number_name = $args[0];
            $plural_type = $args[1];
            $plurals_all = Language::plurals_get($code);
            $number      = array_key_exists($number_name, $transl_args) ? (string)$transl_args[$number_name] : null;
            $plural_info = array_key_exists($plural_type, $plurals_all) ?         $plurals_all[$plural_type] : null;
            if ($number      !== null &&
                $plural_info !== null) {
                $matches = [];
                if (preg_match($plural_info->formula, $number, $matches)) {
                       $replacement = array_intersect_key($plural_info->matches, array_filter($matches, 'strlen'));
                       return reset($replacement);
                } else return '';
            }
        }
    }

    static function filter($string, $code = '**', $strict = false) {
        $result = [];
        $parsed = preg_split('%\\%\\%\\_'.'lang'.'(?<code>\\('.'[a-z\\*]{2}'.'\\)|)%S', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 0, $cur_code = '**'; $i < count($parsed); $i++) {
            if (strlen($parsed[$i]) === 0) { $cur_code = '**';                    continue; }
            if (strlen($parsed[$i]) === 4) { $cur_code = trim($parsed[$i], '()'); continue; }
            if ($cur_code === '**' && $strict === false) $result[] = $parsed[$i];
            if ($cur_code === $code                    ) $result[] = $parsed[$i];
        }
        return $result;
    }

}
