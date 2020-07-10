<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class user {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init($is_load_roles = true) {
    if (static::$cache == null) {
        static::$cache = new instance('user');
        static::$cache->nickname = null;
        static::$cache->id       = null;
        static::$cache->roles    = ['anonymous' => 'anonymous'];
      $session = session::select();
      if ($session &&
          $session->id_user) {
        $user = new instance('user', ['id' => $session->id_user]);
        if ($user->select()) {
          static::$cache = $user;
          static::$cache->roles = $is_load_roles ?
            ['registered' => 'registered'] + static::relation_role_select($session->id_user) :
            ['registered' => 'registered'];
        }
      }
    }
  }

  static function insert($values) {
    return (new instance('user', $values))->insert();
  }

  static function get_current() {
    static::init();
    return static::$cache;
  }

  static function relation_role_select($id_user) {
    $result = [];
    $roles = entity::get('relation_role_ws_user')->instances_select(['conditions' => [
      'id_user_!f' => 'id_user',
      'operator'   => '=',
      'id_user_!v' => $id_user]]);
    foreach ($roles as $c_role)
      $result[$c_role->id_role] =
              $c_role->id_role;
    return $result;
  }

  static function relation_role_insert($id_user, $roles, $module_id = null) {
    foreach ($roles as $c_role) {
      (new instance('relation_role_ws_user', [
        'id_user'   => $id_user,
        'id_role'   => $c_role,
        'module_id' => $module_id
      ]))->insert();
    }
  }

  static function relation_role_delete($id_user, $id_role) {
    (new instance('relation_role_ws_user', [
      'id_user' => $id_user,
      'id_role' => $id_role,
    ]))->delete();
  }

  static function relation_role_delete_all($id_user) {
    entity::get('relation_role_ws_user')->instances_delete(['conditions' => [
      'id_user_!f' => 'id_user',
      'operator'   => '=',
      'id_user_!v' => $id_user
    ]]);
  }

}}