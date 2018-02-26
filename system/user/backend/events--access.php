<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\user as user;
          use \effcore\factory as factory;
          use \effcore\instance as instance;
          abstract class events_access extends \effcore\events_access {

  static function on_check_access_user_delete($page, $id) {
    $user = (new instance('user', ['id' => $id]))->select();
    if ($user) {
      if ($user->is_embed == 1) {
        factory::send_header_and_exit('access_denided');
      }
    } else {
      factory::send_header_and_exit('not_found');
    }
  }

  static function on_check_access_user_edit($page, $id) {
    $user = (new instance('user', ['id' => $id]))->select();
    if ($user) {
      if (!($user->id == user::get_current()->id ||                # not owner or
                   isset(user::get_current()->roles['admins']))) { # not admin
        factory::send_header_and_exit('access_denided');
      }
    } else {
      factory::send_header_and_exit('not_found');
    }
  }

}}