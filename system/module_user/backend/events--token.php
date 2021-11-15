<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\access;
          use \effcore\page;
          use \effcore\user;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    user::init(false);
    if (access::check((object)['roles' => ['registered' => 'registered']])) {
      switch ($name) {
        case 'user_id'              : return user::get_current()->id;
        case 'nickname'             : return user::get_current()->nickname;
        case 'email'                : return user::get_current()->email;
        case 'avatar_path'          : return user::get_current()->avatar_path;
        case 'nickname_page_context': return page::get_current() && !empty($args[0]) ?
                                             page::get_current()->args_get($args[0]) : null;
      }
    }
  }

}}