<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  const return_hex  = 0b00;
  const return_rgb  = 0b01;
  const return_rgba = 0b10;

  public $id;
  public $value;
  public $value_hex;
  public $group;

  function __construct($id = null, $value = null, $value_hex = null, $group = null) {
    if ($id       ) $this->id        = $id;
    if ($value    ) $this->value     = $value;
    if ($value_hex) $this->value_hex = $value_hex;
    if ($group    ) $this->group     = $group;
  }

  function rgb_get($is_int = true) {
    if (!empty($this->value_hex)) {
      $value = ltrim($this->value_hex, '#');
      $parts = [];
      if (strlen($value) === 3) {
        $parts['r'] = $value[0].$value[0];
        $parts['g'] = $value[1].$value[1];
        $parts['b'] = $value[2].$value[2]; }
      if (strlen($value) === 6) {
        $parts['r'] = $value[0].$value[1];
        $parts['g'] = $value[2].$value[3];
        $parts['b'] = $value[4].$value[5]; }
      if (count($parts) && $is_int === true) {
        $parts['r'] = (int)hexdec($parts['r']);
        $parts['g'] = (int)hexdec($parts['g']);
        $parts['b'] = (int)hexdec($parts['b']); }
      return $parts ?: null;
    }
  }

  function filter_shift($r_offset, $g_offset, $b_offset, $opacity = 1, $return_mode = self::return_rgb) {
    $rgb = $this->rgb_get();
    if ($rgb) {
      $new_r = max(min($rgb['r'] + (int)$r_offset, 255), 0);
      $new_g = max(min($rgb['g'] + (int)$g_offset, 255), 0);
      $new_b = max(min($rgb['b'] + (int)$b_offset, 255), 0);
      switch ($return_mode) {
        case static::return_rgb:  return  'rgb('.$new_r.','.$new_g.','.$new_b.             ')';
        case static::return_rgba: return 'rgba('.$new_r.','.$new_g.','.$new_b.','.$opacity.')';
        case static::return_hex:  return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
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

  static function cache_cleaning() {
    static::$cache = null;
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

  static function get($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

}}