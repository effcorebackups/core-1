<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_text;
          use \effcore\field_weight;
          use \effcore\fieldset;
          use \effcore\markup;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        $fieldset_answers = new fieldset('Answers', null, ['data-has-rearrangeable' => 'true']);
        $form->child_select('fields')->child_insert($fieldset_answers, 'answers');
        $form->_answers_rows = entity::get('poll_answer')->instances_select(['conditions' => [
          'id_poll_!f'       => 'id_poll',
          'id_poll_operator' => '=',
          'id_poll_!v'       => $form->_instance->id]]);
        foreach ($form->_answers_rows as $c_answer) {
        # field for text
          $c_field_text = new field_text('Text');
          $c_field_text->description_state = 'hidden';
          $c_field_text->build();
          $c_field_text->name_set('answer_text_'.$c_answer->id);
          $c_field_text->value_set($c_answer->answer);
        # field for weight
          $c_field_weight = new field_weight();
          $c_field_weight->description_state = 'hidden';
          $c_field_weight->build();
          $c_field_weight->name_set('answer_weight_'.$c_answer->id);
          $c_field_weight->required_set(false);
          $c_field_weight->value_set($c_answer->weight);
        # group the fields in widget 'manage'
          $c_widget_manage = new markup('x-widget', ['data-rearrangeable' => 'true', 'data-fields-is-inline' => 'true'], [], $c_answer->weight);
          $c_widget_manage ->child_insert($c_field_weight,  'weight'               );
          $c_widget_manage ->child_insert($c_field_text,    'text'                 );
          $fieldset_answers->child_insert($c_widget_manage, 'manage_'.$c_answer->id);
        }
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
            foreach ($form->_answers_rows as $c_answer) {
              $c_answer->answer = $items['#answer_text_'.  $c_answer->id]->value_get();
              $c_answer->weight = $items['#answer_weight_'.$c_answer->id]->value_get();
              $c_answer->update();
            }
          }
          break;
      }
    }
  }

}}