<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class access {

  static function check($access) {
    foreach (user::current_get()->roles as $c_role) {
      if (isset($access->roles[$c_role])) {
        return true;
      }
    }
  }

  static function roles_get($full = false) {
    $result = [];
    $instances = entity::get('role')->instances_select([], ['weight desc', 'title']);
    foreach ($instances as $c_instance) {
      $result[$c_instance->id] = $full ?
              $c_instance->values :
              $c_instance->title;
    }
    return $result;
  }

}}