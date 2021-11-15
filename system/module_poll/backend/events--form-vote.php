<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\poll {
          use \effcore\access;
          use \effcore\core;
          use \effcore\diagram;
          use \effcore\group_checkboxes;
          use \effcore\group_radiobuttons;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\poll;
          use \effcore\session;
          use \effcore\user;
          abstract class events_form_vote {

  static function on_init($event, $form, $items) {
    $items['~vote'  ]->disabled_set();
    $items['~cancel']->disabled_set();
    $poll = poll::select($form->_id_poll);
    if ($poll) {
      $form->_poll = $poll;
      $form->_id_user = user::get_current()->id;
      $form->_id_session = session::id_get();
      $form->_answers = poll::answers_by_poll_id_select($form->_id_poll);
      $form->_total            = $form->_poll->data['cache']['total'           ] ?? poll::votes_total_select              (array_keys($form->_answers));
      $form->_total_by_answers = $form->_poll->data['cache']['total_by_answers'] ?? poll::votes_id_total_by_answers_select(array_keys($form->_answers));
      if ($form->_id_user) $votes = poll::votes_id_by_user_id_select   ($form->_id_user,    array_keys($form->_answers));
      else                 $votes = poll::votes_id_by_session_id_select($form->_id_session, array_keys($form->_answers));
      $items['fields']->children_delete();
      $items['fields']->title = $poll->question;
    # ─────────────────────────────────────────────────────────────────────
    # voting form
    # ─────────────────────────────────────────────────────────────────────
      if ( ((int)$form->_total < (int)$form->_poll->total_max && $votes === [] && $poll->expired > core::datetime_get() && (int)$poll->user_type === 0) ||
           ((int)$form->_total < (int)$form->_poll->total_max && $votes === [] && $poll->expired > core::datetime_get() && (int)$poll->user_type === 1 && access::check((object)['roles' => ['registered' => 'registered']])) ) {
        $items['~vote']->disabled_set(false);
        $control = $poll->is_multiple ? new group_checkboxes : new group_radiobuttons;
        $control->title = $poll->question;
        $control->title_is_visible = false;
        $control->element_attributes['name'] = 'answers[]';
        $control->required_any = true;
        foreach ($form->_answers as $c_answer)
          $control->field_insert(
            $c_answer->answer, null,
            $c_answer->id, [],
            $c_answer->weight);
        $items['fields']->child_insert($control, 'answers');
    # ─────────────────────────────────────────────────────────────────────
    # voting report
    # ─────────────────────────────────────────────────────────────────────
      } else {
      # build diagram and make report
        $diagram = new diagram(null, $poll->diagram_type);
        $settings = module::settings_get('poll');
        $diagram_colors = core::deep_clone($settings->diagram_colors);
        foreach ($form->_answers as $c_answer) {
          $diagram->slice_insert(                     $c_answer->answer,
            $form->_total ? ($form->_total_by_answers[$c_answer->id] ?? 0) / $form->_total * 100 : 0,
                            ($form->_total_by_answers[$c_answer->id] ?? 0), array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($votes[$c_answer->id]) ? 'true' : null],
                                                      $c_answer->weight
          );
        }
        $items['fields']->child_insert($diagram, 'diagram');
        $items['fields']->child_insert(new markup('x-total', [], [
          new markup('x-title', [], 'Total'),
          new markup('x-value', [], $form->_total)]), 'total'
        );
      # cancellation
        if ((int)$poll->is_cancelable === 1) {
          if ($votes !== []) {
            if ($poll->expired > core::datetime_get()) {
              if ( ((int)$poll->user_type === 0) ||
                   ((int)$poll->user_type === 1 && access::check((object)['roles' => ['registered' => 'registered']])) ) {
                $items['~cancel']->disabled_set(false);
              }
            }
          }
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
      # insert votes by Answer ID or User ID, update cache
        foreach ($form->_poll->is_multiple ? $items['*answers']->values_get() : [$items['*answers']->value_get()] as $c_id_answer)
          if ($form->_id_user) $result = poll::votes_by_user_id_insert   ($form->_id_user,    $c_id_answer);
          else                 $result = poll::votes_by_session_id_insert($form->_id_session, $c_id_answer);
        if ($result) {
               $form->_poll->select();
               $poll_data = $form->_poll->data;
               $poll_data['cache']['total'           ] = poll::votes_total_select              (array_keys($form->_answers));
               $poll_data['cache']['total_by_answers'] = poll::votes_id_total_by_answers_select(array_keys($form->_answers));
               $form->_poll->data = $poll_data;
               $form->_poll->update();
               message::insert('Your answer was accepted.');
        } else message::insert('Your answer was not accepted!', 'error');
        static::on_init(null, $form, $items);
        break;
      case 'cancel':
      # delete votes by Answer ID or User ID, update cache
        if ($form->_id_user) $result = poll::votes_by_user_id_delete   ($form->_id_user,    array_keys($form->_answers));
        else                 $result = poll::votes_by_session_id_delete($form->_id_session, array_keys($form->_answers));
        if ($result) {
               $form->_poll->select();
               $poll_data = $form->_poll->data;
               $poll_data['cache']['total'           ] = poll::votes_total_select              (array_keys($form->_answers));
               $poll_data['cache']['total_by_answers'] = poll::votes_id_total_by_answers_select(array_keys($form->_answers));
               $form->_poll->data = $poll_data;
               $form->_poll->update();
               message::insert('Your answer was canceled.');
        } else message::insert('Your answer was not canceled!', 'error');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}