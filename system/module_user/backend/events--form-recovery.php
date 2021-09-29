<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\mail;
          use \effcore\message;
          use \effcore\url;
          abstract class events_form_recovery {

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        if (!$form->has_error()) {
          if (!(new instance('user', ['email' => $items['#email']->value_get()]))->select()) {
            $items['#email']->error_set(
              'User with this EMail was not registered!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        $user = (new instance('user', [
          'email' => $items['#email']->value_get()
        ]))->select();
        if ($user) {
          $new_password = core::password_generate();
          $user->password_hash = core::password_hash($new_password);
          if ($user->update()) {
            $site_url = url::get_current()->domain;
            if (mail::send('recovery', 'no-reply@'.$site_url, $user, ['domain' => $site_url], ['domain' => $site_url, 'new_password' => $new_password], $form, $items)) {
              message::insert('A new password was sent to the selected EMail.');
              url::go(url::back_url_get() ?: '/login');
            }
          }
        }
        break;
    }
  }

}}