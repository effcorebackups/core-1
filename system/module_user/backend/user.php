<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class user {

  static protected $cache;

  static function init($id = null) {
    static::$cache = new \stdClass;
    static::$cache->id = 0;
    static::$cache->roles = ['anonymous' => 'anonymous'];
  # load user from storage
    if ($id !== null) {
      $user = (new instance('user', [
        'id' => $id
      ]))->select();
      if ($user) {
        static::$cache = (object)($user->get_values());
        static::$cache->roles = ['registered' => 'registered'];
        foreach (entity::get('relation_role_ws_user')->select_instances(['id_user' => $user->id]) as $c_role) {
          static::$cache->roles[$c_role->id_role] = $c_role->id_role;
        }
      }
    }
  }

  static function current_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

}}