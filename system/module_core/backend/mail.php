<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class mail {

  static function send($type, $from, $user, $subject_args, $message_args, $form, $items) {
    $template_subject_name = 'mail_'.$type.'_subject';
    $template_message_name = 'mail_'.$type.'_message';
    if (template::get($template_subject_name) === null) $template_subject_name.= '_embedded';
    if (template::get($template_message_name) === null) $template_message_name.= '_embedded';
    if (template::get($template_subject_name) !== null &&
        template::get($template_message_name) !== null) {
      $mail_encoding = 'Content-Type: text/plain; charset=UTF-8';
      $mail_from = 'From: '.$from;
      $mail_to = $user->nickname.' <'.$user->email.'>';
      $mail_subject = '=?UTF-8?B?'.base64_encode((template::make_new($template_subject_name, $subject_args))->render()).'?=';
      $mail_message =                            (template::make_new($template_message_name, $message_args))->render();
      event::start('on_email_send_before', 'recovery', [
        'to'       => &$mail_to,
        'subject'  => &$mail_subject,
        'body'     => &$mail_message,
        'from'     => &$mail_from,
        'encoding' => &$mail_encoding,
        'form'     => &$form,
        'items'    => &$items
      ]);
      $result = mail(
        $mail_to,
        $mail_subject,
        $mail_message,
        $mail_from.nl.
        $mail_encoding
      );
      if (!$result) message::insert('The letter was not accepted for transmission!', 'error');
      return $result;
    } else {
      if (template::get($template_subject_name) === null) message::insert(new text('Template "%%_name" was not found!', ['name' => $template_subject_name]), 'error');
      if (template::get($template_message_name) === null) message::insert(new text('Template "%%_name" was not found!', ['name' => $template_message_name]), 'error');
    }
  }

}}