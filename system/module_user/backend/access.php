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
      $c_user_permissions = role::related_permissions_by_roles_select($user->roles);
      foreach ($access->permissions as $c_id_permission) {
        if (isset($c_user_permissions[$c_id_permission])) {
          return true;
        }
      }
    }
    if (isset($access->permissions_match)) {
      $c_user_permissions = role::related_permissions_by_roles_select($user->roles);
      foreach ($access->permissions_match as $c_permission_expression) {
        foreach ($c_user_permissions as $c_user_permission) {
          if (preg_match($c_permission_expression, $c_user_permission)) {
            return true;
          }
        }
      }
    }
  }

}}