<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\nl;
          use \effcore\core;
          use \effcore\event;
          use \effcore\message;
          use \effcore\module;
          use \effcore\session;
          use \effcore\template;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_registration {

  const template_mail_registration_subject          = 'mail_registration_subject';
  const template_mail_registration_subject_embedded = 'mail_registration_subject_embedded';
  const template_mail_registration_body             = 'mail_registration_body';
  const template_mail_registration_body_embedded    = 'mail_registration_body_embedded';

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('user');
    $items['#session_params:is_long_session']->description = new text_multiline([
      'Short session: %%_min day%%_plural{min|s} | long session: %%_max day%%_plural{max|s}'], [
      'min' => $settings->session_duration_min,
      'max' => $settings->session_duration_max], '', true, true);
    $items['#password']->disabled_set((bool)$settings->send_password_to_email);
    $items['#email'   ]->value_set('');
    $items['#nickname']->value_set('');
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        $settings = module::settings_get('user');
      # registration via EMail: a password is generated and sent to the user-specified EMail
        if ($settings->send_password_to_email) {
          $new_password = core::password_generate();
          $user = user::insert([
            'email'         => $items['#email'   ]->value_get(),
            'nickname'      => $items['#nickname']->value_get(),
            'timezone'      => $items['#timezone']->value_get(),
            'password_hash' => core::password_hash($new_password)
          ]);
          if ($user) {
            $template_mail_registration_subject_name = template::get(static::template_mail_registration_subject) ? static::template_mail_registration_subject : static::template_mail_registration_subject_embedded;
            $template_mail_registration_body_name    = template::get(static::template_mail_registration_body)    ? static::template_mail_registration_body    : static::template_mail_registration_body_embedded;
            $site_url = url::get_current()->domain;
            $mail_encoding = 'Content-Type: text/plain; charset=UTF-8';
            $mail_from = 'From: no-reply@'.$site_url;
            $mail_to = $user->nickname.' <'.$user->email.'>';
            $mail_subject = '=?UTF-8?B?'.base64_encode((template::make_new($template_mail_registration_subject_name, [
              'domain' => $site_url
            ]))->render()).'?=';
            $mail_body = template::make_new($template_mail_registration_body_name, [
              'domain'       => $site_url,
              'new_password' => $new_password
            ])->render();
            event::start('on_email_send_before', 'registration', [
              'to'       => &$mail_to,
              'subject'  => &$mail_subject,
              'body'     => &$mail_body,
              'from'     => &$mail_from,
              'encoding' => &$mail_encoding,
              'form'     => &$form,
              'items'    => &$items
            ]);
            $mail_send_result = mail(
              $mail_to,
              $mail_subject,
              $mail_body,
              $mail_from.nl.
              $mail_encoding
            );
            if ($mail_send_result) {
                   message::insert('A new password was sent to the selected EMail.'); url::go(url::back_url_get() ?: '/login');
            } else message::insert('The letter was not accepted for transmission.', 'error');
          } else {
            message::insert(
              'User was not registered!', 'error'
            );
          } 
        } else {
        # standard registration: the user sets his own password
          $user = user::insert([
            'email'         => $items['#email'   ]->value_get(),
            'nickname'      => $items['#nickname']->value_get(),
            'timezone'      => $items['#timezone']->value_get(),
            'password_hash' => $items['#password']->value_get()
          ]);
          if ($user) {
            session::insert($user->id,
              core::array_kmap($items['*session_params']->values_get())
            );
            message::insert(
              new text('Welcome, %%_nickname!', ['nickname' => $user->nickname])
            );
            url::go(url::back_url_get() ?: '/user/'.$user->nickname);
          } else {
            message::insert(
              'User was not registered!', 'error'
            );
          }
        }
        break;
    }
  }

}}