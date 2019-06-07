<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class layout extends markup {

  public $tag_name = 'content';
  public $id;
  public $title;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('layouts') as $c_module_id => $c_layouts) {
        foreach ($c_layouts as $c_id => $c_layout) {
          if (isset(static::$cache[$c_id])) console::log_insert_about_duplicate('layout', $c_id, $c_module_id);
          static::$cache[$c_id] = $c_layout;
          static::$cache[$c_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function select_all() {
    static::init();
    return static::$cache;
  }

  static function select($id) {
    static::init();
    return static::$cache[$id];
  }

}}