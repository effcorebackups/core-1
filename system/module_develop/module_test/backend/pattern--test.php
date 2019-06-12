<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class test implements has_external_cache {

  public $id;
  public $title;
  public $description;
  public $params;
  public $scenario;

  function run() {
    $c_results = [];
    foreach ($this->scenario as $c_step) {
      $c_step->run($this, $this->scenario, $c_step, $c_results);
      if (array_key_exists('return', $c_results)) {
        break;
      }
    }
    return $c_results;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return ['id' => 'id', 'title' => 'title'];
  }

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('tests') as $c_module_id => $c_tests) {
        foreach ($c_tests as $c_row_id => $c_test) {
          if (isset(static::$cache[$c_test->id])) console::log_insert_about_duplicate('test', $c_test->id, $c_module_id);
          static::$cache[$c_test->id] = $c_test;
          static::$cache[$c_test->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($id, $load = true) {
    static::init();
    if (isset(static::$cache[$id]) == false) return;
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id];
  }

  static function get_all($load = true) {
    static::init();
    if ($load)
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item = $c_item->external_cache_load();
    return static::$cache;
  }

}}