<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\field_text;
          use \effcore\fieldset;
          use \effcore\markup;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        $fieldset_answers = new fieldset('Answers');
        $form->child_select('fields')->child_insert($fieldset_answers, 'answers');
        $answers = array_chunk($form->_instance->data['answers'] ?? [], 1, true);
        $used_ids = core::array_kmap(range(1, 10));
        for ($i = 0; $i < 10; $i++) {
        # field for answer text
          $c_answer_id   = isset($answers[$i]) ? key    ($answers[$i]) : '';
          $c_answer_text = isset($answers[$i]) ? current($answers[$i]) : '';
          if ($c_answer_id == '') $c_answer_id = current($used_ids);
          unset($used_ids[$c_answer_id]);
          $c_field_answer_text = new field_text('Text');
          $c_field_answer_text->description_state = 'hidden';
          $c_field_answer_text->build();
          $c_field_answer_text->name_set('answer_text_'.$c_answer_id);
          $c_field_answer_text->value_set($c_answer_text);
          $c_field_answer_text->required_set($i == 0);
        # field for answer weight
          $c_field_answer_weight = new field_number('Weight');
          $c_field_answer_weight->description_state = 'hidden';
          $c_field_answer_weight->build();
          $c_field_answer_weight->name_set('answer_weight_'.$c_answer_id);
          $c_field_answer_weight->required_set(false);
          $c_field_answer_weight->value_set(90 - ($i * 10));
          $c_field_answer_weight->min_set(-1000);
          $c_field_answer_weight->max_set(+1000);
        # group fields to box
          $c_box_answer = new markup('x-box', ['data-field-order-type' => 'inline']);
          $c_box_answer    ->child_insert($c_field_answer_weight, 'answer_weight');
          $c_box_answer    ->child_insert($c_field_answer_text,   'answer_text'  );
          $fieldset_answers->child_insert($c_box_answer,          'answer_'.   $i);
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
            $answers_unsorted = [];
            $answers          = [];
            for ($c_answer_id = 1; $c_answer_id <= 10; $c_answer_id++)
              if ($items['#answer_text_'.$c_answer_id]->value_get())
                $answers_unsorted[] = (object)[
                  'id'     => $c_answer_id,
                  'weight' => $items['#answer_weight_'.$c_answer_id]->value_get(),
                  'text'   => $items['#answer_text_'.  $c_answer_id]->value_get()];
            core::array_sort_by_weight($answers_unsorted);
            foreach ($answers_unsorted as $c_answer)
              $answers[$c_answer->id] = $c_answer->text;
            $form->_instance->data = ['answers' => $answers];
          }
          break;
      }
    }
  }

}}