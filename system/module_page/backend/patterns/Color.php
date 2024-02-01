<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Color {

    const RETURN_HEX  = 0b00;
    const RETURN_RGB  = 0b01;
    const RETURN_RGBA = 0b10;

    public $id;
    public $value_hex;
    public $group;
    public $origin = 'nosql'; # nosql | nosql-dynamic
    public $module_id;

    function __construct($id = null, $value_hex = null, $group = null, $module_id = null, $origin = null) {
        if ($id       ) $this->id        = $id;
        if ($value_hex) $this->value_hex = $value_hex;
        if ($group    ) $this->group     = $group;
        if ($module_id) $this->module_id = $module_id;
        if ($origin   ) $this->origin    = $origin;
    }

    function HSV_get() {$rgb = $this->RGB_get(); if ($rgb) return static::RGB_to_HSV($rgb['r'], $rgb['g'], $rgb['b']);}
    function HSL_get() {$rgb = $this->RGB_get(); if ($rgb) return static::RGB_to_HSL($rgb['r'], $rgb['g'], $rgb['b']);}
    function RGB_get($is_int = true) {
        if (!empty($this->value_hex)) {
            $value = ltrim($this->value_hex, '#');
            if (strlen($value) === 3 && $is_int !== true) return ['r' =>             $value[0].$value[0],  'g' =>             $value[1].$value[1],  'b' =>             $value[2].$value[2]];
            if (strlen($value) === 6 && $is_int !== true) return ['r' =>             $value[0].$value[1],  'g' =>             $value[2].$value[3],  'b' =>             $value[4].$value[5]];
            if (strlen($value) === 3 && $is_int === true) return ['r' => (int)hexdec($value[0].$value[0]), 'g' => (int)hexdec($value[1].$value[1]), 'b' => (int)hexdec($value[2].$value[2])];
            if (strlen($value) === 6 && $is_int === true) return ['r' => (int)hexdec($value[0].$value[1]), 'g' => (int)hexdec($value[2].$value[3]), 'b' => (int)hexdec($value[4].$value[5])];
        }
    }

    function filter_shift($r_shift, $g_shift, $b_shift, $opacity = 1, $return_mode = self::RETURN_RGB) {
        $rgb = $this->RGB_get();
        if ($rgb) {
            $new_r = Security::sanitize_min_max(0, 255, (int)$rgb['r'] + (int)$r_shift);
            $new_g = Security::sanitize_min_max(0, 255, (int)$rgb['g'] + (int)$g_shift);
            $new_b = Security::sanitize_min_max(0, 255, (int)$rgb['b'] + (int)$b_shift);
            $new_o = Security::sanitize_min_max(0, 1, $opacity);
            if ($return_mode === static::RETURN_RGB ) return  'rgb('.$new_r.','.$new_g.','.$new_b.')';
            if ($return_mode === static::RETURN_RGBA) return 'rgba('.$new_r.','.$new_g.','.$new_b.','.$new_o.')';
            if ($return_mode === static::RETURN_HEX ) {
                return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
                           str_pad(dechex($new_g), 2, '0', STR_PAD_LEFT).
                           str_pad(dechex($new_b), 2, '0', STR_PAD_LEFT);
            }
        }
    }

    function is_dark() { # @return: true | false | null
        $rgb = $this->RGB_get();
        if ($rgb) {
            return $rgb['r'] +
                   $rgb['g'] +
                   $rgb['b'] <= 127 * 3;
        }
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
            foreach (Storage::get('data')->select_array('colors') as $c_module_id => $c_colors) {
                foreach ($c_colors as $c_row_id => $c_color) {
                    if (isset(static::$cache[$c_color->id])) Console::report_about_duplicate('colors', $c_color->id, $c_module_id, static::$cache[$c_color->id]);
                              static::$cache[$c_color->id] = $c_color;
                              static::$cache[$c_color->id]->module_id = $c_module_id;
                }
            }
            $custom_colors = Module::settings_get('page')->custom_colors;
            if (count($custom_colors)) {
                foreach ($custom_colors as $c_id => $c_hex_value) {
                    $custom_colors[$c_id] = new static(
                        $c_id, $c_hex_value, 'custom', 'page', 'nosql-dynamic'
                    );
                }
                uasort($custom_colors, function ($color_a, $color_b) {
                   $hsv_a = $color_a->HSV_get();
                   $hsv_b = $color_b->HSV_get();
                   if ($hsv_a && $hsv_b) {
                       return (($hsv_a['h'] <=> $hsv_b['h']) * 0xfff) +
                              (($hsv_a['s'] <=> $hsv_b['s']) * 0x0ff) +
                              (($hsv_a['v'] <=> $hsv_b['v']) * 0x00f);
                   }
                });
                foreach ($custom_colors as $c_color) {
                   static::$cache[$c_color->id] = $c_color;
                   static::$cache[$c_color->id]->origin = 'nosql-dynamic';
                }
            }
        }
    }

    static function get($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

    static function get_all($origin = null) {
        static::init();
        $result = static::$cache ?? [];
        if ($origin)
            foreach ($result as $c_id => $c_item)
                if ($c_item->origin !== $origin)
                    unset($result[$c_id]);
        return $result;
    }

    static function changes_store($values = [], $module_id = 'page') {
        $result = ['colors' => [], 'cache_update' => true];
        foreach ($values as $c_id => $c_hex_value) {
            if ($c_hex_value !== null) $result['colors'][$c_id] = Storage::get('data')->changes_register  ($module_id, 'insert', 'settings/page/custom_colors/'.$c_id, $c_hex_value, false);
            if ($c_hex_value === null) $result['colors'][$c_id] = Storage::get('data')->changes_unregister($module_id, 'insert', 'settings/page/custom_colors/'.$c_id,               false);
        }
        $result['cache_update'] = Storage_Data::cache_update();
        return $result;
    }

    static function RGB_to_HSV($r, $g, $b) {
        $r = Security::sanitize_min_max(0, 255, (int)$r);
        $g = Security::sanitize_min_max(0, 255, (int)$g);
        $b = Security::sanitize_min_max(0, 255, (int)$b);
        $result = [];
        $min = min($r, $g, $b);
        $max = max($r, $g, $b);
        $delta_min_max = $max - $min;
        $result_h = 0;
        if     ($delta_min_max !== 0 && $max === $r && $g >= $b) $result_h = 60 * (($g - $b) / $delta_min_max) +   0;
        elseif ($delta_min_max !== 0 && $max === $r && $g <  $b) $result_h = 60 * (($g - $b) / $delta_min_max) + 360;
        elseif ($delta_min_max !== 0 && $max === $b            ) $result_h = 60 * (($r - $g) / $delta_min_max) + 240;
        elseif ($delta_min_max !== 0 && $max === $g            ) $result_h = 60 * (($b - $r) / $delta_min_max) + 120;
        $result_s = $max === 0 ? 0 : (1 - ($min / $max));
        $result_v = $max;
        $result['h'] = (int)(round($result_h));
        $result['s'] = (int)($result_s * 100);
        $result['v'] = (int)($result_v / 2.55);
        return $result;
    }

    static function RGB_to_HSL($r, $g, $b) {
        $r = Security::sanitize_min_max(0, 255, (int)$r);
        $g = Security::sanitize_min_max(0, 255, (int)$g);
        $b = Security::sanitize_min_max(0, 255, (int)$b);
        $result = [];
        $min = min($r, $g, $b);
        $max = max($r, $g, $b);
        $delta_min_max = $max - $min;
        $result_h = 0;
        if     ($delta_min_max !== 0 && $max === $r && $g >= $b) $result_h = 60 * (($g - $b) / $delta_min_max) +   0;
        elseif ($delta_min_max !== 0 && $max === $r && $g <  $b) $result_h = 60 * (($g - $b) / $delta_min_max) + 360;
        elseif ($delta_min_max !== 0 && $max === $b            ) $result_h = 60 * (($r - $g) / $delta_min_max) + 240;
        elseif ($delta_min_max !== 0 && $max === $g            ) $result_h = 60 * (($b - $r) / $delta_min_max) + 120;
        $p = (int)($max / 2.55) / 100;
        $q = (int)($max === 0 ? 0 : (1 - ($min / $max)) * 100) / 100;
        $result_l = $p * (1 - ($q / 2));
        $result_s = 0;
        $k = min($result_l, 1 - $result_l);
        if ($result_l !== 0 && $result_l !== 1 && $k !== 0 && $k !== 0.0) {
            $result_s = ($p - $result_l) / $k;
        }
        $result['h'] = (int)(round($result_h));
        $result['s'] = (int)($result_s * 100);
        $result['l'] = (int)($result_l * 100);
        return $result;
    }

}
