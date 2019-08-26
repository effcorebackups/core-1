<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class breadcrumbs extends markup {

  public $tag_name = 'x-breadcrumbs';
  public $id;

  function build() {
    if (!$this->is_builded) {
      event::start('on_breadcrumbs_build', $this->id, [&$this]);
      $this->is_builded = true;
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('breadcrumbs') as $c_module_id => $c_breadcrumbs) {
        foreach ($c_breadcrumbs as $c_breadcrumb) {
          if (isset(static::$cache[$c_breadcrumb->id])) console::log_insert_about_duplicate('breadcrumbs', $c_breadcrumb->id, $c_module_id);
          static::$cache[$c_breadcrumb->id] = $c_breadcrumb;
          static::$cache[$c_breadcrumb->id]->module_id = $c_module_id;
          static::$cache[$c_breadcrumb->id]->type = 'nosql';
        }
      }
    }
  }

  static function select($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

}}