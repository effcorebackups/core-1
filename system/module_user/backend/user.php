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
            ['registered' => 'registered'] + static::related_roles_select($session->id_user) :
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

  static function related_roles_select($id_user) {
    $result = [];
    $items = entity::get('relation_role_ws_user')->instances_select(['conditions' => [
      'id_user_!f' => 'id_user',
      'operator'   => '=',
      'id_user_!v' => $id_user]]);
    foreach ($items as $c_item)
      $result[$c_item->id_role] =
              $c_item->id_role;
    return $result;
  }

  static function related_roles_insert($id_user, $roles, $module_id = null) {
    foreach ($roles as $c_id_role) {
      (new instance('relation_role_ws_user', [
        'id_role'   => $c_id_role,
        'id_user'   =>   $id_user,
        'module_id' => $module_id
      ]))->insert();
    }
  }

  static function related_roles_delete($id_user) {
    entity::get('relation_role_ws_user')->instances_delete(['conditions' => [
      'id_user_!f' => 'id_user',
      'operator'   => '=',
      'id_user_!v' => $id_user
    ]]);
  }

  static function related_role_delete($id_user, $id_role) {
    (new instance('relation_role_ws_user', [
      'id_user' => $id_user,
      'id_role' => $id_role
    ]))->delete();
  }

}}