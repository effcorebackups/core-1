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
      $form->_id_answers = [];
    # get answers by Poll ID
      $answers_row = entity::get('poll_answer')->instances_select(['conditions' => [
        'id_poll_!f'       => 'id_poll',
        'id_poll_operator' => '=',
        'id_poll_!v'       => $form->_id_poll]]);
      foreach ($answers_row as $c_row)
        $form->_id_answers[$c_row->id] =
                           $c_row->id;
    # get votes by Answer ID and User ID
      $votes_row = entity::get('poll_vote')->instances_select(['conditions' => [
        'id_user_!f'         => 'id_user',
        'id_user_operator'   => '=',
        'id_user_!v'         => $form->_id_user,
        'conjunction'        => 'and',
        'id_answer_!f'       => 'id_answer',
        'id_answer_in_begin' => 'in (',
        'id_answer_in_!,'    => entity::get('poll_vote')->storage_get()->prepare_values($form->_id_answers),
        'id_answer_in_end'   => ')']]);
      $votes = [];
      foreach ($votes_row as $c_row)
        $votes[$c_row->id_answer] =
               $c_row->id_answer;
    # init form elements
      $items['fields']->children_delete();
      $items['fields']->title = $poll->question;
    # ─────────────────────────────────────────────────────────────────────
    # voting form
    # ─────────────────────────────────────────────────────────────────────
      if ( ($poll->expired > core::datetime_get() && $votes == [] && $poll->user_type == 0) ||
           ($poll->expired > core::datetime_get() && $votes == [] && $poll->user_type == 1 && access::check((object)['roles' => ['registered' => 'registered']])) ) {
        $items['~vote']->disabled_set(false);
        $control = $poll->is_multiple ? new group_checkboxes : new group_radiobuttons;
        $control->title = $poll->question;
        $control->title_is_visible = false;
        $control->required_any = true;
        $control->build();
        $items['fields']->child_insert($control, 'answers');
        foreach ($answers_row as $c_answer) {
          $control->field_insert(
            $c_answer->answer, null,
            $c_answer->id, ['name' => 'answers[]'],
            $c_answer->weight
          );
        }
    # ─────────────────────────────────────────────────────────────────────
    # voting report
    # ─────────────────────────────────────────────────────────────────────
      } else {
      # make statistics
        $total = entity::get('poll_vote')->instances_select_count(['conditions' => [
          'id_answer_!f'       => 'id_answer',
          'id_answer_in_begin' => 'in (',
          'id_answer_in_!,'    => $form->_id_answers,
          'id_answer_in_end'   => ')']]);
        $total_by_answers_rows = entity::get('poll_vote')->instances_select([
          'fields'     => ['id_answer_!f' => 'id_answer', 'count' => ['function_begin' => 'count(', 'function_field' => '*', 'function_end' => ')', 'alias_begin' => 'as', 'alias' => 'total']],
          'conditions' => ['id_answer_!f' => 'id_answer', 'id_answer_in_begin' => 'in (', 'id_answer_in_!,' => $form->_id_answers, 'id_answer_in_end' => ')'],
          'group'      => ['id_answer_!f' => 'id_answer']]);
        $total_by_answers = [];
        foreach ($total_by_answers_rows as $c_row)
          $total_by_answers[$c_row->id_answer] =
                            $c_row->total;
      # build diagram
        $diagram = new diagram(null, $poll->diagram_type);
        $diagram_colors = core::diagram_colors;
        foreach ($answers_row as $c_answer) {
          $diagram->slice_insert($c_answer->answer,
            $total ? ($total_by_answers[$c_answer->id] ?? 0) / $total * 100 : 0,
                      $total_by_answers[$c_answer->id] ?? 0,
            array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($votes[$c_answer->id]) ? 'true' : null], $c_answer->weight
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
        new markup('x-no-items', ['data-style' => 'table'], 'no items')
      );
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'vote':
        foreach ($form->_poll->is_multiple ? $items['*answers']->values_get() : [$items['*answers']->value_get()] as $c_id_answer)
          $result = (new instance('poll_vote', [
            'id_user'   => $form->_id_user,
            'id_answer' => $c_id_answer
          ]))->insert();
        if ($result) message::insert('Your answer was accepted.'             );
        else         message::insert('Your answer was not accepted!', 'error');
        static::on_init(null, $form, $items);
        break;
      case 'cancel':
      # delete votes by Answer ID and User ID
        $result = entity::get('poll_vote')->instances_delete(['conditions' => [
          'id_user_!f'         => 'id_user',
          'id_user_operator'   => '=',
          'id_user_!v'         => $form->_id_user,
          'conjunction'        => 'and',
          'id_answer_!f'       => 'id_answer',
          'id_answer_in_begin' => 'in (',
          'id_answer_in_!,'    => $form->_id_answers,
          'id_answer_in_end'   => ')']]);
        if ($result) message::insert('Your answer was canceled.'             );
        else         message::insert('Your answer was not canceled!', 'error');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}