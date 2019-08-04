<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\text_multiline;
          use \effcore\user;
          abstract class events_page_user {

  static function on_build_before($event, $page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nick')
    ]))->select();
    if ($user) {
      if ($user->nick == user::get_current()->nick ||             # owner
                   isset(user::get_current()->roles['admins'])) { # admin
      } else core::send_header_and_exit('access_forbidden');
    }   else core::send_header_and_exit('page_not_found'  );
  }

  static function on_show_user_roles($c_row, $c_instance) {
    return new text_multiline(
      user::id_roles_get($c_instance->id), [], ', '
    );
  }

}}