<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
        static::$cache = (object)($user->values_get());
        static::$cache->roles = static::id_roles_get($user->id);
        static::$cache->roles['registered'] = 'registered';
      }
    }
  }

  static function current_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function id_roles_get($id) {
    $id_roles = [];
    $roles = entity::get('relation_role_ws_user')->instances_select(['id_user' => $id]);
    foreach ($roles as $c_role)
      $id_roles[$c_role->id_role] =
                $c_role->id_role;
    return $id_roles;
  }

}}