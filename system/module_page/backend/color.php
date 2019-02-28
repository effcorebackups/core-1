<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    foreach (storage::get('files')->select('colors') as $c_module_id => $c_colors) {
      foreach ($c_colors as $c_row_id => $c_color) {
        if (isset(static::$cache[$c_color->id])) console::log_about_duplicate_insert('color', $c_color->id, $c_module_id);
        static::$cache[$c_color->id] = $c_color;
        static::$cache[$c_color->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id] ?? null;
  }

  static function all_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

}}