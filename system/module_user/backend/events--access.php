<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\user;
          use \effcore\core;
          use \effcore\instance;
          abstract class events_access extends \effcore\events_access {

  static function on_check_access_user_delete($page) {
    $user = (new instance('user', [
      'id' => $page->args_get('id_user')
    ]))->select();
    if ($user) {
      if ($user->is_embed == 1) {
        core::send_header_and_exit('access_denided');
      }
    } else {
      core::send_header_and_exit('not_found');
    }
  }

  static function on_check_access_user_edit($page) {
    $user = (new instance('user', [
      'id' => $page->args_get('id_user')
    ]))->select();
    if ($user) {
      if (!($user->id == user::get_current()->id ||                # not owner or
                   isset(user::get_current()->roles['admins']))) { # not admin
        core::send_header_and_exit('access_denided');
      }
    } else {
      core::send_header_and_exit('not_found');
    }
  }

}}