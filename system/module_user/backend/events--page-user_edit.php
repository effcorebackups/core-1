<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\user;
          abstract class events_page_user_edit {

  static function on_page_init($page) {
    $user = (new instance('user', [
      'nick' => $page->args_get('nick')
    ]))->select();
    if ($user) {
      if (!($user->nick == user::current_get()->nick ||              # not owner or
                     isset(user::current_get()->roles['admins']))) { # not admin
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