<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\diagram;
          use \effcore\entity;
          use \effcore\group_radiobuttons;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\storage;
          use \effcore\user;
          abstract class events_form_poll {

  const diagram_colors = [
    '#216ce4',
    '#48be38',
    '#fc5740',
    '#fd9a1e',
    'lightseagreen',
    'springgreen',
    'yellowgreen',
    'gold',
    'crimson',
    'lightcoral'
  ];

  static function on_init($event, $form, $items) {
    $items['~vote']->disabled_set();
    $poll = new instance('poll', ['id' => 1]);
    $entity_poll_vote = entity::get('poll_vote');
    $storage = storage::get($entity_poll_vote->storage_name);
    if ($poll->select()) {
      $form->_id_poll = $poll              ->id;
      $form->_id_user = user::get_current()->id;
      $answers_rows = $storage->query([
        'action'          => 'SELECT',
        'fields_!,'       => ['all_!f' => '*'],
        'target_begin'    => 'FROM',
        'target_!t'       => '~poll_vote',
        'condition_begin' => 'WHERE',
        'condition'       => [
        'id_poll_!f'      => 'id_poll', 'operator_1' => '=', 'id_poll_!v' => $form->_id_poll, 'conjunction' => 'AND',
        'id_user_!f'      => 'id_user', 'operator_2' => '=', 'id_user_!v' => $form->_id_user]]);
      $answers = [];
      foreach ($answers_rows as $c_row)
        $answers[$c_row->id_answer] =
                 $c_row->id_answer;
      $items['fields']->children_delete();
      $items['fields']->title = $poll->question;
    # ─────────────────────────────────────────────────────────────────────
    # voting form
    # ─────────────────────────────────────────────────────────────────────
      if ($answers == [] && $poll->expired > core::datetime_get()) {
        $items['~vote']->disabled_set(false);
        $radiobuttons = new group_radiobuttons();
        $radiobuttons->build();
        $items['fields']->child_insert($radiobuttons, 'answers');
        foreach ($poll->data['answers'] as $c_id => $c_text) {
          $radiobuttons->field_insert(
            $c_text, null, ['name' => 'answers', 'value' => $c_id], $c_id
          );
        }
    # ─────────────────────────────────────────────────────────────────────
    # voting report
    # ─────────────────────────────────────────────────────────────────────
      } else {
      # make statistics
        $total = $entity_poll_vote->instances_select_count(['conditions' => [
          'id_poll_!f'      => 'id_poll',
          'operator'        => '=',
          'id_poll_!v'      => $form->_id_poll]]);
        $total_by_answer_rows = $storage->query([
          'action'          => 'SELECT',
          'fields_!,'       => [
          'id_answer_!f'    => 'id_answer',
          'count'           => [
          'function_begin'  => 'count(',
          'function_field'  => '*',
          'function_end'    => ')',
          'alias_begin'     => 'as',
          'alias'           => 'total']],
          'target_begin'    => 'FROM',
          'target_!t'       => '~poll_vote',
          'condition_begin' => 'WHERE',
          'condition'       => [
          'id_poll_!f'      => 'id_poll',
          'operator'        => '=',
          'id_poll_!v'      =>  $form->_id_poll],
          'group_begin'     => 'GROUP BY',
          'group_fields_!,' => [
          'id_answer_!f'    => 'id_answer']]);
        $total_by_answer = [];
        foreach ($total_by_answer_rows as $c_row)
          $total_by_answer[$c_row->id_answer] = $c_row->total;
      # build diagram
        $diagram = new diagram('', $poll->diagram_type);
        $diagram_colors = self::diagram_colors;
        foreach ($poll->data['answers'] as $c_id => $c_text)
          $diagram->slice_insert($c_text,
            $total ? ($total_by_answer[$c_id] ?? 0) / $total * 100 : 0,
                      $total_by_answer[$c_id] ?? 0,
            array_shift($diagram_colors), ['data-id' => $c_id, 'aria-selected' => isset($answers[$c_id]) ? 'true' : false]
          );
      # make report
        $items['fields']->child_insert($diagram, 'diagram');
        $items['fields']->child_insert(new markup('x-total', [], [
          new markup('x-title', [], 'Total'),
          new markup('x-value', [], $total)]), 'total'
        );
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
        $result = (new instance('poll_vote', [
          'id_poll'   => $form->_id_poll,
          'id_user'   => $form->_id_user,
          'id_answer' => $items['*answers']->value_get()
        ]))->insert();
        if ($result) message::insert('Your answer was accepted.'    );
        else         message::insert('Your answer was not accepted!');
        static::on_init($event, $form, $items);
        break;
    }
  }

}}