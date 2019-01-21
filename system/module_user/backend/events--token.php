<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\instance;
          use \effcore\session;
          use \effcore\url;
          use \effcore\user;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    $session = session::select();
    if ($session &&
        $session->id_user) {
      $user = user::current_get();
      if (!isset($user->roles['registered'])) {
        user::init($session->id_user, false);
        $user = user::current_get();
      }
      if (isset($user->roles['registered'])) {
        switch ($name) {
          case 'id'        : return     user::current_get()->id;
          case 'nick'      : return     user::current_get()->nick;
          case 'email'     : return     user::current_get()->email;
          case 'avatar_url': return '/'.user::current_get()->avatar_path;
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
  }

}}