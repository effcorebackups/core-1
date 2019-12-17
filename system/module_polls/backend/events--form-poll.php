<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\access;
          use \effcore\core;
          use \effcore\diagram;
          use \effcore\entity;
          use \effcore\group_checkboxes;
          use \effcore\group_radiobuttons;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\user;
          abstract class events_form_poll {

  static function on_init($event, $form, $items) {
    $items['~vote'  ]->disabled_set();
    $items['~cancel']->disabled_set();
    $poll = new instance('poll', ['id' => $form->_id_poll]);
    if ($poll->select()) {
      $form->_poll = $poll;
      $form->_id_user = user::get_current()->id;
    # get votes by Poll ID and User ID
      $vote_rows = entity::get('poll_vote')->instances_select(['conditions' => [
        'id_poll_!f' => 'id_poll', 'id_poll_operator' => '=', 'id_poll_!v' => $form->_id_poll, 'conjunction' => 'and',
        'id_user_!f' => 'id_user', 'id_user_operator' => '=', 'id_user_!v' => $form->_id_user]]);
      $votes = [];
      foreach ($vote_rows as $c_row)
        $votes[$c_row->id_answer] =
               $c_row->id_answer;
    # get answers by Poll ID
      $answers_rows = entity::get('poll_answer')->instances_select(['conditions' => [
        'id_poll_!f'       => 'id_poll',
        'id_poll_operator' => '=',
        'id_poll_!v'       => $form->_id_poll]]);
      $items['fields']->children_delete();
      $items['fields']->title = $poll->question;
    # ─────────────────────────────────────────────────────────────────────
    # voting form
    # ─────────────────────────────────────────────────────────────────────
      if ( ($poll->expired > core::datetime_get() && $votes == [] && $poll->user_type == 0) ||
           ($poll->expired > core::datetime_get() && $votes == [] && $poll->user_type == 1 && access::check((object)['roles' => ['registered' => 'registered']])) ) {
        $items['~vote']->disabled_set(false);
        $selector = $poll->is_multiple ? new group_checkboxes : new group_radiobuttons;
        $selector->build();
        $items['fields']->child_insert($selector, 'answers');
        foreach ($answers_rows as $c_answer) {
          $selector->field_insert(
            $c_answer->answer, null, ['name' => 'answers[]', 'value' => $c_answer->id], $c_answer->id, $c_answer->weight
          );
        }
    # ─────────────────────────────────────────────────────────────────────
    # voting report
    # ─────────────────────────────────────────────────────────────────────
      } else {
      # make statistics
        $total = entity::get('poll_vote')->instances_select_count(['conditions' => [
          'id_poll_!f' => 'id_poll',
          'operator'   => '=',
          'id_poll_!v' => $form->_id_poll]]);
        $total_by_answers_rows = entity::get('poll_vote')->instances_select([
          'fields'     => ['id_!f' => 'id_answer', 'count' => ['function_begin' => 'count(', 'function_field' => '*', 'function_end' => ')', 'alias_begin' => 'as', 'alias' => 'total']],
          'conditions' => ['id_poll_!f' => 'id_poll', 'id_poll_operator' => '=', 'id_poll_!v' => $form->_id_poll],
          'group'      => ['id_!f' => 'id_answer']]);
        $total_by_answers = [];
        foreach ($total_by_answers_rows as $c_row)
          $total_by_answers[$c_row->id_answer] =
                            $c_row->total;
      # build diagram
        $diagram = new diagram(null, $poll->diagram_type);
        $diagram_colors = core::diagram_colors;
        foreach ($answers_rows as $c_answer) {
          $diagram->slice_insert($c_answer->answer,
            $total ? ($total_by_answers[$c_answer->id] ?? 0) / $total * 100 : 0,
                      $total_by_answers[$c_answer->id] ?? 0,
            array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($votes[$c_answer->id]) ? 'true' : false], $c_answer->weight
          );
        }
      # make report
        $items['fields']->child_insert($diagram, 'diagram');
        $items['fields']->child_insert(new markup('x-total', [], [
          new markup('x-title', [], 'Total'),
          new markup('x-value', [], $total)]), 'total'
        );
      # cancellation
        if ($poll->expired > core::datetime_get() &&
            $poll->is_cancelable == 1 &&
            $poll->user_type     == 1 && access::check((object)['roles' => ['registered' => 'registered']])) {
          $items['~cancel']->disabled_set(false);
        }
      }
    } else {
      $form->child_update('fields',
        new markup('x-no-result', [], 'no items')
      );
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
        foreach ($form->_poll->is_multiple ? $items['*answers']->values_get() : [$items['*answers']->value_get()] as $c_id_answer)
          $result = (new instance('poll_vote', [
            'id_poll'   => $form->_id_poll,
            'id_user'   => $form->_id_user,
            'id_answer' => $c_id_answer
          ]))->insert();
        if ($result) message::insert('Your answer was accepted.'             );
        else         message::insert('Your answer was not accepted!', 'error');
        static::on_init($event, $form, $items);
        break;
      case 'cancel':
        $result = entity::get('poll_vote')->instances_delete(['conditions' => [
          'id_poll_!f' => 'id_poll', 'id_poll_operator' => '=', 'id_poll_!v' => $form->_id_poll, 'conjunction' => 'and',
          'id_user_!f' => 'id_user', 'id_user_operator' => '=', 'id_user_!v' => $form->_id_user]]);
        if ($result) message::insert('Your answer was canceled.'             );
        else         message::insert('Your answer was not canceled!', 'error');
        static::on_init($event, $form, $items);
        break;
    }
  }

}}