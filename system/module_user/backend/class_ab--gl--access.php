<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class access {

  static function check($access) {
    foreach (user::get_current()->roles as $c_role) {
      if (isset($access->roles[$c_role])) {
        return true;
      }
    }
  }

}}