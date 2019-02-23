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
          $user->password_hash = core::password_hash_get($new_password);
          if ($user->update()) {
            $current_url = url::current_get();
            $mail_encoding = 'Content-Type: text/plain; charset=UTF-8';
            $mail_from = 'From: no-reply@'.$current_url->domain;
            $mail_to = $user->nick.' <'.$user->email.'>';
            $mail_subject = clone $form->mail_subject;
            $mail_subject->args = ['domain' => $current_url->domain];
            $mail_subject = '=?UTF-8?B?'.base64_encode($mail_subject->render()).'?=';
            $mail_body = clone $form->mail_body;
            $mail_body->delimiter = nl.nl;
            $mail_body->args = ['domain' => $current_url->domain, 'new_password' => $new_password];
            $mail_body = $mail_body->render();
            $mail_send_result = mail(
              $mail_to,
              $mail_subject,
              $mail_body,
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