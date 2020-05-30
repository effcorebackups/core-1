<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class access {

  static function check($access, $user = null) {
    if ($access === null) return true;
    if (  $user === null) $user = user::get_current();
    if (isset($access->roles)) foreach ($user->roles as $c_role) if (isset($access->roles[ $c_role ])) return true;
    if (isset($access->users))                                   if (isset($access->users[$user->id])) return true;
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