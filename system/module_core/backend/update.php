<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class update {

  static function is_required() {
    foreach (module::get_all() as $c_module) {
      $c_updates            = static::select_all        ($c_module->id);
      $c_update_last_number = static::select_last_number($c_module->id);
      foreach ($c_updates as $c_update) {
        if ($c_update->number > $c_update_last_number) {
          return true;
        }
      }
    }
  }

  static function select_all($module_id, $from_number = 0) {
    $result = [];
    foreach (storage::get('data')->select_array('modules_update_data', false, false) as $c_module_id => $c_updates)
      if ($c_module_id === $module_id)
        foreach ($c_updates as $c_row_id => $c_update)
          if ($c_update->number >= $from_number)
            $result[$c_row_id] = $c_update;
    return $result;
  }

  static function select_last_number($module_id) {
    $info = new instance('update', ['module_id' => $module_id]);
    return $info->select() ? (int)$info->last_number : 0;
  }

  static function insert_last_number($module_id, $last_number) {
    $info = new instance('update', ['module_id' => $module_id]);
    if ($info->select()) return (new instance('update', ['module_id' => $module_id, 'last_number' => $last_number]))->update();
    else                 return (new instance('update', ['module_id' => $module_id, 'last_number' => $last_number]))->insert();
  }

}}