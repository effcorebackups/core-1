<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color_preset {

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('files')->select('colors_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_row_id => $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::report_about_duplicate('colors_presets', $c_preset->id, $c_module_id);
          static::$cache[$c_preset->id] = $c_preset;
          static::$cache[$c_preset->id]->module_id = $c_module_id;
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