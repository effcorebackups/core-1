<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\field_email;
          use \effcore\field_nick;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\session;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_registration {

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        if (!$form->has_error()) {
        # test email
          if (!field_email::validate_uniqueness(
            $items['#email'],
            $items['#email']->value_get()
          )) return;
        # test nick
          if (!field_nick::validate_uniqueness(
            $items['#nick'],
            $items['#nick']->value_get()
          )) return;
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        $user = user::insert([
          'email'         => strtolower($items['#email'   ]->value_get()),
          'nick'          =>            $items['#nick'    ]->value_get(),
          'timezone'      =>            $items['#timezone']->value_get(),
          'password_hash' =>            $items['#password']->value_get()
        ]);
        if ($user) {
          session::insert($user->id,
            core::array_kmap($items['*session_params']->values_get())
          );
          message::insert_to_storage(
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