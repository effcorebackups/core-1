<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class selection
          implements has_external_cache {

  function make() {
    //print_r($this);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function not_external_properties_get() {
    return [];
  }

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('selections') as $c_module_id => $c_selections) {
      foreach ($c_selections as $c_row_id => $c_selection) {
        if (isset(static::$cache[$c_row_id])) console::log_about_duplicate_add('selection', $c_row_id);
        static::$cache[$c_row_id] = $c_selection;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id, $load = true) {
    if (static::$cache == null) static::init();
    if (static::$cache[$row_id] instanceof external_cache && $load)
        static::$cache[$row_id] = static::$cache[$row_id]->external_cache_load();
    return static::$cache[$row_id] ?? null;
  }

}}