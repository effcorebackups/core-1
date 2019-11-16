<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\poll {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_text;
          use \effcore\fieldset;
          use \effcore\markup;
          use \effcore\page;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        if ($items['#expired']->value_get() == null)
            $items['#expired']->value_set(core::datetime_get('+'.core::date_period_w.' second'));
        $fieldset_answers = new fieldset('Answers');
        $form->child_select('fields')->child_insert($fieldset_answers, 'answers');
        for ($i = 0; $i < 10; $i++) {
        # field for answer text
          $c_field_answer_text = new field_text('Text');
          $c_field_answer_text->description_state = 'hidden';
          $c_field_answer_text->build();
          $c_field_answer_text->name_set('answer_text[]');
          $c_field_answer_text->required_set($i == 0);
        # group field to box
          $c_box_answer = new markup('x-box', ['data-field-order-type' => 'inline']);
          $c_box_answer->child_insert($c_field_answer_text, 'answer_text');
          $fieldset_answers->child_insert($c_box_answer, 'answer_'.$i);
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
          if ($entity->name == 'poll') {
            $answers = [];
            foreach ($items['#answer_text'] as $c_field_answer_text)
              if ($c_field_answer_text->value_get())
                $answers[count($answers) + 1] = $c_field_answer_text->value_get();
            $form->_instance->data = ['answers' => $answers];
          }
          break;
      }
    }
  }

}}