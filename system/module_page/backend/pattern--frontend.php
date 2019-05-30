<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class frontend {

  public $display;
  public $favicons;
  public $styles;
  public $scripts;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('frontend') as $c_module_id => $c_frontends) {
        foreach ($c_frontends as $c_row_id => $c_frontend) {
          if (isset(static::$cache[$c_row_id])) console::log_insert_about_duplicate('frontend', $c_row_id, $c_module_id);
          static::$cache[$c_row_id] = $c_frontend;
          static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function get($row_id) {
    static::init();
    return static::$cache[$row_id];
  }

}}