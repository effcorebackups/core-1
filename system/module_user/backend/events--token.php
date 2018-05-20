<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\instance;
          use \effcore\url;
          use \effcore\user;
          abstract class events_token extends \effcore\events_token {

  static function on_replace($name, $args = []) {
    if (!empty(user::get_current()->id)) {
      switch ($name) {
        case 'id_user': return user::get_current()->id;
        case 'email'  : return user::get_current()->email;
        case 'nick'   : return user::get_current()->nick;
        case 'id_user_context':
        case 'email_context':
        case 'nick_context':
          $arg_num = $args[0];
          $id_user = url::get_current()->get_path_arg($arg_num);
          $user = (new instance('user', ['id' => $id_user]))->select();
          if ($user && $name == 'id_user_context') return $user->id;
          if ($user && $name == 'email_context')   return $user->email;
          if ($user && $name == 'nick_context')    return $user->nick;
          return '[unknown uid]';
      }
    }
  }

}}