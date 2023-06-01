<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class color {

    const RETURN_HEX  = 0b00;
    const RETURN_RGB  = 0b01;
    const RETURN_RGBA = 0b10;

    public $id;
    public $value_hex;
    public $group;

    function __construct($id = null, $value_hex = null, $group = null) {
        if ($id       ) $this->id        = $id;
        if ($value_hex) $this->value_hex = $value_hex;
        if ($group    ) $this->group     = $group;
    }

    function rgb_get($is_int = true) {
        if (!empty($this->value_hex)) {
            $value = ltrim($this->value_hex, '#');
            if (strlen($value) === 3 && $is_int !== true) return ['r' =>             $value[0].$value[0],  'g' =>             $value[1].$value[1],  'b' =>             $value[2].$value[2]];
            if (strlen($value) === 6 && $is_int !== true) return ['r' =>             $value[0].$value[1],  'g' =>             $value[2].$value[3],  'b' =>             $value[4].$value[5]];
            if (strlen($value) === 3 && $is_int === true) return ['r' => (int)hexdec($value[0].$value[0]), 'g' => (int)hexdec($value[1].$value[1]), 'b' => (int)hexdec($value[2].$value[2])];
            if (strlen($value) === 6 && $is_int === true) return ['r' => (int)hexdec($value[0].$value[1]), 'g' => (int)hexdec($value[2].$value[3]), 'b' => (int)hexdec($value[4].$value[5])];
        }
    }

    function filter_shift($r_offset, $g_offset, $b_offset, $opacity = 1, $return_mode = self::RETURN_RGB) {
        $rgb = $this->rgb_get();
        if ($rgb) {
            $new_r = max(min($rgb['r'] + (int)$r_offset, 255), 0);
            $new_g = max(min($rgb['g'] + (int)$g_offset, 255), 0);
            $new_b = max(min($rgb['b'] + (int)$b_offset, 255), 0);
            if ($return_mode === static::RETURN_RGB ) return  'rgb('.$new_r.','.$new_g.','.$new_b.')';
            if ($return_mode === static::RETURN_RGBA) return 'rgba('.$new_r.','.$new_g.','.$new_b.','.$opacity.')';
            if ($return_mode === static::RETURN_HEX ) {
                return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
                           str_pad(dechex($new_g), 2, '0', STR_PAD_LEFT).
                           str_pad(dechex($new_b), 2, '0', STR_PAD_LEFT);
            }
        }
    }

    function is_dark() { # @return: true | false | null
        $rgb = $this->rgb_get();
        if ($rgb) {
            return $rgb['r'] +
                   $rgb['g'] +
                   $rgb['b'] <= 127 * 3;
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static protected $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (storage::get('data')->select_array('colors') as $c_module_id => $c_colors) {
                foreach ($c_colors as $c_row_id => $c_color) {
                    if (isset(static::$cache[$c_color->id])) console::report_about_duplicate('colors', $c_color->id, $c_module_id, static::$cache[$c_color->id]);
                              static::$cache[$c_color->id] = $c_color;
                              static::$cache[$c_color->id]->module_id = $c_module_id;
                }
            }
        }
        $custom_colors = [];
        foreach (static::$cache as $id => $c_color) {
            if ($c_color->module_id !== 'page') {
                $custom_colors[$id] = $c_color;
                unset(static::$cache[$id]);
            }
        }
        if (count($custom_colors)) {
            static::$cache =
            static::$cache + $custom_colors;
        }
    }

    static function get($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

}
