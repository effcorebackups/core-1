<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
      $instances = entity::get('role')->instances_select([
        'order' => ['fields_!,' => [
          'weight' => ['weight_!f' => 'weight', 'weight_type' => 'DESC'],
          'title'  => [ 'title_!f' => 'title',   'title_type' => 'ASC' ] ]] ]);
      foreach ($instances as $c_instance) {
        $c_role = new static;
        foreach ($c_instance->values_get() as $c_key => $c_value)
          $c_role->                          {$c_key} = $c_value;
        static::$cache[$c_role->id] = $c_role;
        static::$cache[$c_role->id]->origin = 'sql';
      }
    }
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function get_all() {
    static::init_sql();
    return static::$cache;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function related_permissions_select($id_role) {
    $result = [];
    $items = entity::get('relation_role_ws_permission')->instances_select(['conditions' => [
      'id_role_!f'       => 'id_role',
      'id_role_operator' => '=',
      'id_role_!v'       => $id_role]]);
    foreach ($items as $c_item)
      $result[$c_item->id_permission] =
              $c_item->id_permission;
    return $result;
  }

  static function related_permissions_by_roles_select($roles) {
    $result = [];
    $items = entity::get('relation_role_ws_permission')->instances_select(['conditions' => [
      'id_role_!f'           => 'id_role',
      'id_in_begin_operator' => 'in (',
      'id_in_!v'             => $roles,
      'id_in_end_operator'   => ')']]);
    foreach ($items as $c_item)
      $result[$c_item->id_permission] =
              $c_item->id_permission;
    return $result;
  }

  static function related_permissions_insert($id_role, $permissions, $module_id = null) {
    foreach ($permissions as $c_id_permission) {
      (new instance('relation_role_ws_permission', [
        'id_permission' => $c_id_permission,
        'id_role'       => $id_role,
        'module_id'     => $module_id
      ]))->insert();
    }
  }

  static function related_permissions_delete($id_role) {
    entity::get('relation_role_ws_permission')->instances_delete(['conditions' => [
      'id_role_!f'       => 'id_role',
      'id_role_operator' => '=',
      'id_role_!v'       => $id_role
    ]]);
  }

  static function related_permission_delete($id_role, $id_permission) {
    (new instance('relation_role_ws_permission', [
      'id_role'       => $id_role,
      'id_permission' => $id_permission
    ]))->delete();
  }

}}