<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class access {

  static function check($access, $user = null) {
    if ($access === null) return true;
    if (  $user === null) $user = user::get_current();
    if (isset($access->roles      )) foreach ($user->roles as $c_role) if (isset($access->roles[ $c_role ])) return true;
    if (isset($access->users      ))                                   if (isset($access->users[$user->id])) return true;
    if (isset($access->permissions)) {
      foreach ($access->permissions as $c_id_permission) {
        $c_permission_roles = permission::get_roles_by_permission($c_id_permission);
        foreach ($user->roles as $c_role) {
          if (isset($c_permission_roles[$c_role])) {
            return true;
          }
        }
      }
    }
  }

}}