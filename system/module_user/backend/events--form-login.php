<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\br;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\session;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_login {

  static function on_init($form, $items) {
    if (!isset($_COOKIE['cookies_is_on'])) {
      message::insert(
        translation::get('Cookies are disabled. You can not log in!').br.
        translation::get('Enable cookies before login.'), 'warning'
      );
    }
  }

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'login':
        if (!$form->has_error()) {
          $user = (new instance('user', [
            'email' => strtolower($items['#email']->value_get())
          ]))->select();
          if (!$user || (
               $user->password_hash !== core::hash_password_get($items['#password']->value_get()))) {
            $items['#email'   ]->error_set();
            $items['#password']->error_set();
            $form->error_set('Incorrect email or password!');
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'login':
        $user = (new instance('user', [
          'email' => strtolower($items['#email']->value_get())
        ]))->select();
        if ($user &&
            $user->password_hash === core::hash_password_get($items['#password']->value_get())) {
          session::insert($user->id,
            core::array_kmap($items['*session_params']->values_get())
          );
          url::go('/user/'.$user->nick);
        }
        break;
    }
  }

}}