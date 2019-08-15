<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color {

  public $id;
  public $value;
  public $value_hex;
  public $group;

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
    if (static::$cache == null) {
      foreach (storage::get('files')->select('colors') as $c_module_id => $c_colors) {
        foreach ($c_colors as $c_row_id => $c_color) {
          if (isset(static::$cache[$c_color->id])) console::log_insert_about_duplicate('color', $c_color->id, $c_module_id);
          static::$cache[$c_color->id] = $c_color;
          static::$cache[$c_color->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function init_presets() {
    if (static::$cache_presets == null) {
      foreach (storage::get('files')->select('colors_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_row_id => $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::log_insert_about_duplicate('color_preset', $c_preset->id, $c_module_id);
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