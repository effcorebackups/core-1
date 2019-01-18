<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\cr;
          use const \effcore\nl;
          use \effcore\core;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_recovery {

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        if (!$form->has_error()) {
          if (!(new instance('user', ['email' => strtolower($items['#email']->value_get())]))->select()) {
            $items['#email']->error_set(
              'User with this EMail was not registered!'
            );
            return;
          }
        }
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        $user = (new instance('user', [
          'email' => strtolower($items['#email']->value_get())
        ]))->select();
        if ($user) {
          $new_password = core::password_generate();
          $user->password_hash = core::hash_password_get($new_password);
          if ($user->update()) {
            $current_url = url::current_get();
            $mail_encoding = 'Content-Type: text/plain; charset=UTF-8';
            $mail_from = 'From: no-reply@'.$current_url->domain;
            $mail_to = $user->nick.' <'.$user->email.'>';
            $mail_subject = new text('Password recovery on %%_domain', ['domain' => $current_url->domain]);
            $mail_message = new text_multiline([
              'You received this message because someone tried to recover the password from your %%_domain account.',
              'Your new password on %%_domain has been changed automatically to: %%_new_password',
              'Your EMail is not shown publicly on %%_domain and is never shared with third parties!'], [
              'domain'       => $current_url->domain,
              'new_password' => $new_password
            ], nl.nl);
            $mail_send_result = mail(
              $mail_to,
              $mail_subject->render(),
              $mail_message->render(),
              $mail_from.nl.
              $mail_encoding
            );
            if ($mail_send_result)
                 message::insert('A new password has been sent to selected EMail.');
            else message::insert('The letter was not accepted for transmission.', 'error');
          }
        }
        break;
    }
  }

}}