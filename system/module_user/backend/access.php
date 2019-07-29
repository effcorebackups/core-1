<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class access {

  static function check($access) {
    if ($access === null) return true;
    foreach (user::get_current()->roles as $c_role) {
      if (isset($access->roles[$c_role])) {
        return true;
      }
    }
  }

  static function roles_get($full = false) {
    $result = [];
    $instances = entity::get('role')->instances_select([
      'order' => ['weight_!f' => 'weight', 'DESC', ',', 'title_!f' => 'title', 'ASC']
    ]);
    foreach ($instances as $c_instance) {
      $result[$c_instance->id] = $full ?
              $c_instance->values :
              $c_instance->title;
    }
    return $result;
  }

}}