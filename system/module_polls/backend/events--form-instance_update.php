<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\fieldset;
          use \effcore\page;
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
          'id_poll_!v'       => $form->_instance->id]]);
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
            //foreach ($form->_answers_rows as $c_answer) {
            //  $c_answer->answer = $items['#answer_text_'.  $c_answer->id]->value_get();
            //  $c_answer->weight = $items['#answer_weight_'.$c_answer->id]->value_get();
            //  $c_answer->update();
            //}
          }
          break;
      }
    }
  }

}}