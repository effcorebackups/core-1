<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\fieldset;
          use \effcore\instance;
          use \effcore\page;
          use \effcore\url;
          use \effcore\widget_poll_fields;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        page::get_current()->args_set('back_update_is_canceled', true);
        $form->_answers_rows = entity::get('poll_answer')->instances_select(['conditions' => [
          'id_poll_!f'       => 'id_poll',
          'id_poll_operator' => '=',
          'id_poll_!v'       => $form->_instance->id]], 'id');
        $widget_items = [];
        foreach ($form->_answers_rows as $c_row) {
          $widget_items[] = (object)[
            'id'     => $c_row->id,
            'weight' => $c_row->weight,
            'text'   => $c_row->answer
          ];
        }
        $widget_answers = new widget_poll_fields('answer_');
        $widget_answers->form_current_set($form);
        $widget_answers->items_set_once($widget_items);
        $widget_answers->build();
        $fieldset_answers = new fieldset('Answers');
        $fieldset_answers->child_insert($widget_answers, 'answers');
        $form->child_select('fields')->child_insert($fieldset_answers, 'answers');
        $form->_widget_answers = $widget_answers;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'poll') {
            $used_ids = [];
            foreach ($form->_widget_answers->items_get() as $c_item) {
            # insert new answer
              if ($c_item->id == 0) {
                (new instance('poll_answer', [
                  'id_poll' => $form->_instance->id,
                  'answer'  => $c_item->text,
                  'weight'  => $c_item->weight
                ]))->insert();
              }
            # update current answer
              if ($c_item->id != 0) {
                $form->_answers_rows[$c_item->id]->answer = $c_item->text;
                $form->_answers_rows[$c_item->id]->weight = $c_item->weight;
                $form->_answers_rows[$c_item->id]->update();
                $used_ids[$c_item->id] =
                          $c_item->id;
              }
            }
          # delete old answers
            foreach ($form->_answers_rows as $c_row) {
              if (!isset($used_ids[$c_row->id])) {
                $c_row->delete();
              }
            }
          # reset unactual recordset
            $form->_answers_rows = null;
          # going back
            url::go($back_update_0 ?: (url::back_url_get() ?: (
                    $back_update_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          }
          break;
      }
    }
  }

}}