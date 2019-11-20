<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\message;
          abstract class events_form_poll {

  static function on_init($event, $form, $items) {
    $poll = new instance('poll', ['id' => 1]);
    if ($poll->select()) {
      $items['fields']->title = $poll->question;
      if ($poll->expired > core::datetime_get()) {
        foreach ($poll->data['answers'] as $c_id => $c_text) {
          $items['*answers']->field_insert(
            $c_text, null, ['value' => $c_id], $c_id
          );
        }
      } else {
      }
    }
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'vote':
        if (!$items['*answers']->value_get()) {
          message::insert('No one item was selected!', 'warning');
          $items['*answers']->error_set();
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'vote':
        message::insert('ok');
        break;
    }
  }

}}