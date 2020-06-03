<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class role {

  public $id;
  public $title;
  public $weight;

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
      $instances = entity::get('role')->instances_select(['order' => ['weight_!f' => 'weight', 'DESC', ',', 'title_!f' => 'title', 'ASC']]);
      foreach ($instances as $c_instance) {
        $c_role = new static;
        foreach ($c_instance->values_get() as $c_key => $c_value)
          $c_role->                          {$c_key} = $c_value;
        static::$cache[$c_role->id] = $c_role;
        static::$cache[$c_role->id]->module_id = 'user';
        static::$cache[$c_role->id]->origin = 'sql';
      }
    }
  }

  static function select_all() {
    static::init_sql();
    return static::$cache;
  }

}}