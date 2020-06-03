<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class permission {

  public $id;
  public $title;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $is_init___sql = false;

  static function cache_cleaning() {
    static::$cache = null;
    static::$is_init___sql = false;
  }

  static function init_sql() {
    if (!static::$is_init___sql) {
         static::$is_init___sql = true;
      foreach (entity::get('permission')->instances_select() as $c_instance) {
        $c_permission = new static;
        foreach ($c_instance->values_get() as $c_key => $c_value)
          $c_permission->                    {$c_key} = $c_value;
        static::$cache[$c_permission->id] = $c_permission;
        static::$cache[$c_permission->id]->module_id = 'user';
        static::$cache[$c_permission->id]->origin = 'sql';
      }
    }
  }

  static function get_all() {
    static::init_sql();
    return static::$cache;
  }

}}