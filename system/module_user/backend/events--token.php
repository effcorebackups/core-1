<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\instance;
          use \effcore\page;
          use \effcore\session;
          use \effcore\url;
          use \effcore\user;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    $session = session::select();
    if ($session &&
        $session->id_user) {
      $user = user::get_current();
      if (!isset($user->roles['registered'])) {
        user::init($session->id_user, false);
        $user = user::get_current();
      }
      if (isset($user->roles['registered'])) {
        switch ($name) {
          case 'user_id'   : return     user::get_current()->id;
          case 'nick'      : return     user::get_current()->nick;
          case 'email'     : return     user::get_current()->email;
          case 'avatar_url': return '/'.user::get_current()->avatar_path;
          case 'nick_page_context':
            if ($args[0] == 'nick') {
              return page::get_current()->get_args('nick');
            }
        }
      }
    }
    return '';
  }

}}