<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\Access;
use effcore\Core;
use effcore\Diagram;
use effcore\Group_Checkboxes;
use effcore\Group_Radiobuttons;
use effcore\Markup;
use effcore\Message;
use effcore\Module;
use effcore\Poll;
use effcore\Session;
use effcore\User;

abstract class Events_Form_Vote {

    static function on_build($event, $form) {
        $form->_mode = null;
        $poll = Poll::select($form->_id_poll);
        if ($poll) {
            $form->_poll = $poll;
            $form->_id_user = User::get_current()->id;
            $form->_id_session = Session::id_get();
            $form->_answers = Poll::answers_by_poll_id_select($form->_id_poll);
            $form->_total            = $form->_poll->data['cache']['total'           ] ?? Poll::votes_total_select              (array_keys($form->_answers));
            $form->_total_by_answers = $form->_poll->data['cache']['total_by_answers'] ?? Poll::votes_id_total_by_answers_select(array_keys($form->_answers));
            if ($form->_id_user) $form->_votes = Poll::votes_id_by_user_id_select   ($form->_id_user,    array_keys($form->_answers));
            else                 $form->_votes = Poll::votes_id_by_session_id_select($form->_id_session, array_keys($form->_answers));
            $form->child_select('fields')->children_delete();
            $form->child_select('fields')->title = $form->_poll->question;

            # ─────────────────────────────────────────────────────────────────────
            # voting form
            # ─────────────────────────────────────────────────────────────────────

            if ( ((int)$form->_total < (int)$form->_poll->total_max && $form->_votes === [] && $form->_poll->expired > Core::datetime_get() && (int)$form->_poll->user_type === 0) ||
                 ((int)$form->_total < (int)$form->_poll->total_max && $form->_votes === [] && $form->_poll->expired > Core::datetime_get() && (int)$form->_poll->user_type === 1 && Access::check((object)['roles' => ['registered' => 'registered']])) ) {
                $form->_mode = 'form';
                $control = $form->_poll->is_multiple ? new Group_Checkboxes : new Group_Radiobuttons;
                $control->title = $form->_poll->question;
                $control->title_is_visible = false;
                $control->element_attributes['name'] = 'answers[]';
                $control->required_any = true;
                $control_items = [];
                foreach ($form->_answers as $c_answer)
                    $control_items[$c_answer->id] = (object)['title' => $c_answer->answer, 'weight' => $c_answer->weight];
                $control->items_set($control_items);
                $form->child_select('fields')->child_insert($control, 'answers');

            # ─────────────────────────────────────────────────────────────────────
            # voting report
            # ─────────────────────────────────────────────────────────────────────

            } else {
                # build diagram and make report
                $form->_mode = 'report';
                $diagram = new Diagram(null, $form->_poll->diagram_type);
                $settings = Module::settings_get('poll');
                $diagram_colors = Core::deep_clone($settings->diagram_colors);
                foreach ($form->_answers as $c_answer) {
                    $diagram->slice_insert(                       $c_answer->answer,
                        $form->_total ? ($form->_total_by_answers[$c_answer->id] ?? 0) / $form->_total * 100 : 0,
                                        ($form->_total_by_answers[$c_answer->id] ?? 0), array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($form->_votes[$c_answer->id]) ? 'true' : null],
                                                                  $c_answer->weight
                    );
                }
                $form->child_select('fields')->child_insert($diagram, 'diagram');
                $form->child_select('fields')->child_insert(new Markup('x-total', [], [
                    new Markup('x-title', [], 'Total'),
                    new Markup('x-value', [], $form->_total)]), 'total'
                );
            }
        } else {
            $form->child_update('fields',
                new Markup('x-no-items', ['data-style' => 'table'], 'No items.')
            );
        }
    }

    static function on_init($event, $form, $items) {
        $items['~vote'  ]->disabled_set();
        $items['~cancel']->disabled_set();
        switch ($form->_mode) {
            case 'form':
                $items['~vote']->disabled_set(false);
                break;
            case 'report':
                if ((int)$form->_poll->is_cancelable === 1) {
                    if ($form->_votes !== []) {
                        if ($form->_poll->expired > Core::datetime_get()) {
                            if ( ((int)$form->_poll->user_type === 0) ||
                                 ((int)$form->_poll->user_type === 1 && Access::check((object)['roles' => ['registered' => 'registered']])) ) {
                                $items['~cancel']->disabled_set(false);
                            }
                        }
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'vote':
                # insert votes by Answer ID or User ID, update cache
                foreach ($form->_poll->is_multiple ? $items['*answers']->value_get() : [$items['*answers']->value_get()] as $c_id_answer)
                    if ($form->_id_user) $result = Poll::votes_by_user_id_insert   ($form->_id_user,    $c_id_answer);
                    else                 $result = Poll::votes_by_session_id_insert($form->_id_session, $c_id_answer);
                if ($result) {
                    $form->_poll->select();
                    $poll_data = $form->_poll->data;
                    $poll_data['cache']['total'           ] = Poll::votes_total_select              (array_keys($form->_answers));
                    $poll_data['cache']['total_by_answers'] = Poll::votes_id_total_by_answers_select(array_keys($form->_answers));
                    $form->_poll->data = $poll_data;
                    $form->_poll->update();
                       Message::insert('Your answer was accepted.');
                } else Message::insert('Your answer was not accepted!', 'error');
                static::on_build(null, $form);
                static::on_init (null, $form, $form->items_update());
                break;
            case 'cancel':
                # delete votes by Answer ID or User ID, update cache
                if ($form->_id_user) $result = Poll::votes_by_user_id_delete   ($form->_id_user,    array_keys($form->_answers));
                else                 $result = Poll::votes_by_session_id_delete($form->_id_session, array_keys($form->_answers));
                if ($result) {
                    $form->_poll->select();
                    $poll_data = $form->_poll->data;
                    $poll_data['cache']['total'           ] = Poll::votes_total_select              (array_keys($form->_answers));
                    $poll_data['cache']['total_by_answers'] = Poll::votes_id_total_by_answers_select(array_keys($form->_answers));
                    $form->_poll->data = $poll_data;
                    $form->_poll->update();
                       Message::insert('Your answer was canceled.');
                } else Message::insert('Your answer was not canceled!', 'error');
                static::on_build(null, $form);
                static::on_init (null, $form, $form->items_update());
                break;
        }
    }

}
