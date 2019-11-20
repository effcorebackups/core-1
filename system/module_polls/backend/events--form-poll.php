<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\group_radiobuttons;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\storage;
          use \effcore\user;
          abstract class events_form_poll {

  static function on_init($event, $form, $items) {
    $poll = new instance('poll', ['id' => 1]);
    if ($poll->select()) {
      $form->_id_poll = $poll              ->id;
      $form->_id_user = user::get_current()->id;
      $storage = storage::get(entity::get('poll_vote')->storage_name);
      $result_answer = $storage->query([
        'action'          => 'SELECT',
        'fields_!,'       => ['all_!f' => '*'],
        'target_begin'    => 'FROM',
        'target_!t'       => '~poll_vote',
        'condition_begin' => 'WHERE',
        'condition'       => [
        'id_poll_!f'      => 'id_poll', 'operator_1' => '=', 'id_poll_!v' => $form->_id_poll, 'conjunction' => 'AND',
        'id_user_!f'      => 'id_user', 'operator_2' => '=', 'id_user_!v' => $form->_id_user]]);
      $items['fields']->children_delete();
      $items['fields']->title = $poll->question;
      if (!isset($result_answer[0]->id_answer) && $poll->expired > core::datetime_get()) {
        $radiobuttons = new group_radiobuttons();
        $radiobuttons->build();
        $items['fields']->child_insert($radiobuttons, 'answers');
        foreach ($poll->data['answers'] as $c_id => $c_text) {
          $radiobuttons->field_insert(
            $c_text, null, ['name' => 'answers', 'value' => $c_id], $c_id
          );
        }
      } else {
        $items['~vote']->disabled_set();
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
        $result_vote = (new instance('poll_vote', [
          'id_poll'   => $form->_id_poll,
          'id_user'   => $form->_id_user,
          'id_answer' => $items['*answers']->value_get()
        ]))->insert();
        if ($result_vote) message::insert('Your answer was accepted.'    );
        else              message::insert('Your answer was not accepted!');
        static::on_init($event, $form, $items);
        break;
    }
  }

}}