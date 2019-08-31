<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\message;
          use \effcore\session;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_registration {

  static function on_init($event, $form, $items) {
    $items['#email']->value_set('');
    $items['#nick' ]->value_set('');
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        $user = user::insert([
          'email'         => $items['#email'   ]->value_get(),
          'nick'          => $items['#nick'    ]->value_get(),
          'timezone'      => $items['#timezone']->value_get(),
          'password_hash' => $items['#password']->value_get()
        ]);
        if ($user) {
          session::insert($user->id,
            core::array_kmap($items['*session_params']->values_get())
          );
          message::insert(
            new text('Welcome, %%_nick!', ['nick' => $user->nick])
          );
          url::go('/user/'.$user->nick);
        } else {
          message::insert(
            'User was not registered!', 'error'
          );
        }
        break;
    }
  }

}}