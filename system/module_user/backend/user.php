<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class user implements has_cache_cleaning {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init($nick = null) {
    static::$cache = new instance('user', ['nick' => null, 'roles' => ['anonymous' => 'anonymous']]);
    if ($nick != null) {
      $user = new instance('user', ['nick' => $nick]);
      if ($user->select()) {
        $user->roles = static::id_roles_get($nick) + ['registered' => 'registered'];
        static::$cache = $user;
      }
    }
  }

  static function insert($values) {
    return (new instance('user', $values))->insert();
  }

  static function current_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function id_roles_get($nick) {
    $id_roles = [];
    $roles = entity::get('relation_role_ws_user')->instances_select(['nick' => $nick]);
    foreach ($roles as $c_role)
      $id_roles[$c_role->id_role] =
                $c_role->id_role;
    return $id_roles;
  }

}}