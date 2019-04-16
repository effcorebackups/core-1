<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  static protected $cache;
  static protected $cache_presets;

  static function cache_cleaning() {
    static::$cache         = null;
    static::$cache_presets = null;
  }

  static function init() {
    foreach (storage::get('files')->select('colors') as $c_module_id => $c_colors) {
      foreach ($c_colors as $c_row_id => $c_color) {
        if (isset(static::$cache[$c_color->id])) console::log_insert_about_duplicate('color', $c_color->id, $c_module_id);
        static::$cache[$c_color->id] = $c_color;
        static::$cache[$c_color->id]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('colors_presets') as $c_module_id => $c_presets) {
      foreach ($c_presets as $c_row_id => $c_preset) {
        if (isset(static::$cache[$c_preset->id])) console::log_insert_about_duplicate('color_preset', $c_preset->id, $c_module_id);
        static::$cache_presets[$c_preset->id] = $c_preset;
        static::$cache_presets[$c_preset->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id] ?? null;
  }

  static function get_all() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function preset_get($id) {
    if    (static::$cache_presets == null) static::init();
    return static::$cache_presets[$id] ?? null;
  }

  static function preset_get_all() {
    if    (static::$cache_presets == null) static::init();
    return static::$cache_presets;
  }

}}