<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\session;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_registration {

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        if (!form::$errors) {
        # test email
          if ((new instance('user', ['email' => strtolower($items['#email']->value_get())]))->select()) {
            $items['#email']->error_set(
              'User with this EMail was already registered!'
            );
            return;
          }
        # test nick
          if ((new instance('user', ['nick' => strtolower($items['#nick']->value_get())]))->select()) {
            $items['#nick']->error_set(
              'User with this Nick was already registered!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        $user = user::insert([
          'email'         => strtolower($items['#email']->value_get()),
          'nick'          => strtolower($items['#nick' ]->value_get()),
          'password_hash' => core::hash_password_get($items['#password']->value_get())
        ]);
        if ($user) {
          session::insert($user->nick,
            core::array_kmap($items['*session_params']->values_get())
          );
          url::go('/user/'.$user->nick);
        } else {
          message::insert('User was not registered!', 'error');
        }
        break;
    }
  }

}}