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

}}