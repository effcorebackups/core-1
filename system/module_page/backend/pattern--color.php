<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  const return_hex = 0b00;
  const return_rgb = 0b01;
  const return_rga = 0b10;

  public $id;
  public $value;
  public $value_hex;
  public $group;

  function rgb_get($is_int = true) {
    if (!empty($this->value_hex)) {
      $value = ltrim($this->value_hex, '#');
      $value_parts = [];
      if (strlen($value) === 3) {
        $value_parts['r'] = $value[0].$value[0];
        $value_parts['g'] = $value[1].$value[1];
        $value_parts['b'] = $value[2].$value[2];}
      if (strlen($value) === 6) {
        $value_parts['r'] = $value[0].$value[1];
        $value_parts['g'] = $value[2].$value[3];
        $value_parts['b'] = $value[4].$value[5];}
      if ($value_parts && $is_int) {
        $value_parts['r'] = (int)hexdec($value_parts['r']);
        $value_parts['g'] = (int)hexdec($value_parts['g']);
        $value_parts['b'] = (int)hexdec($value_parts['b']);}
      return $value_parts ?: null;
    }
  }

  function filter_shift($r_offset, $g_offset, $b_offset, $opacity = 1, $return_mode = self::return_rgb) {
    $rgb = $this->rgb_get();
    if ($rgb) {
      $new_r = max(min($rgb['r'] + (int)$r_offset, 255), 0);
      $new_g = max(min($rgb['g'] + (int)$g_offset, 255), 0);
      $new_b = max(min($rgb['b'] + (int)$b_offset, 255), 0);
      switch ($return_mode) {
        case static::return_rgb: return  'rgb('.$new_r.','.$new_g.','.$new_b.             ')';
        case static::return_rga: return 'rgba('.$new_r.','.$new_g.','.$new_b.','.$opacity.')';
        case static::return_hex: return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
                                            str_pad(dechex($new_g), 2, '0', STR_PAD_LEFT).
                                            str_pad(dechex($new_b), 2, '0', STR_PAD_LEFT);
      }
    }
  }

  function is_dark() { # return: true | false | null
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
  static protected $cache_presets;

  static function cache_cleaning() {
    static::$cache         = null;
    static::$cache_presets = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('files')->select('colors') as $c_module_id => $c_colors) {
        foreach ($c_colors as $c_row_id => $c_color) {
          if (isset(static::$cache[$c_color->id])) console::report_about_duplicate('color', $c_color->id, $c_module_id);
                    static::$cache[$c_color->id] = $c_color;
                    static::$cache[$c_color->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function init_presets() {
    if (static::$cache_presets === null) {
      foreach (storage::get('files')->select('colors_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_row_id => $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::report_about_duplicate('colors_presets', $c_preset->id, $c_module_id);
          static::$cache_presets[$c_preset->id] = $c_preset;
          static::$cache_presets[$c_preset->id]->module_id = $c_module_id;
        }
      }
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

  static function preset_get($id) {
    static::init_presets();
    return static::$cache_presets[$id] ?? null;
  }

  static function preset_get_all() {
    static::init_presets();
    return static::$cache_presets;
  }

}}