<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\user;
          abstract class events_page_user_edit {

  static function on_before_build($page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nick')
    ]))->select();
    if ($user) {
      if (!($user->nick == user::get_current()->nick ||              # not owner or
                     isset(user::get_current()->roles['admins']))) { # not admin
        core::send_header_and_exit('access_forbidden');
      } else {
        $page->args_set('entity_name', 'user');
        $page->args_set('instance_id', $user->id);
      }
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

}}