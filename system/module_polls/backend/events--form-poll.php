<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\storage;
          use \effcore\user;
          abstract class events_form_poll {

  static function on_init($event, $form, $items) {
    $form->_id_poll = 1;
    $poll = new instance('poll', ['id' => $form->_id_poll]);
    if ($poll->select()) {
      $id_poll = $poll              ->id;
      $id_user = user::get_current()->id;
      $storage = storage::get(entity::get('poll_vote')->storage_name);
      $result_answer = $storage->query([
        'action'          => 'SELECT',
        'fields_!,'       => ['all_!f' => '*'],
        'target_begin'    => 'FROM',
        'target_!t'       => '~poll_vote',
        'condition_begin' => 'WHERE',
        'condition'       => [
        'id_poll_!f'      => 'id_poll', 'operator_1' => '=', 'id_poll_!v' => $id_poll, 'conjunction' => 'AND',
        'id_user_!f'      => 'id_user', 'operator_2' => '=', 'id_user_!v' => $id_user],
      ]);
      $items['*answers']->children_delete();
      $items['fields']->title = $poll->question;
      if (!isset($result_answer[0]->id_answer) && $poll->expired > core::datetime_get()) {
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
          $form->error_set();
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'vote':
        $result_vote = (new instance('poll_vote', [
          'id_poll'   => $form->_id_poll,
          'id_answer' => $items['*answers']->value_get(),
          'id_user'   => user::get_current()->id
        ]))->insert();
        if ($result_vote) message::insert('Your answer was accepted.'    );
        else              message::insert('Your answer was not accepted!');
        static::on_init($event, $form, $items);
        break;
    }
  }

}}