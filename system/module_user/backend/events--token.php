<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\instance;
          use \effcore\url;
          use \effcore\user;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    if (!empty(user::current_get()->nick)) {
      switch ($name) {
        case 'email': return user::current_get()->email;
        case 'nick' : return user::current_get()->nick;
        case 'email_context':
        case 'nick_context':
          $arg_number = $args[0];
          $nick = url::current_get()->path_arg_select($arg_number);
          $user = (new instance('user', ['nick' => $nick]))->select();
          if ($user && $name == 'email_context') return $user->email;
          if ($user && $name == 'nick_context')  return $user->nick;
          return '[unknown nick]';
      }
    }
  }

}}