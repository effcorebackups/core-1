<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_text;
          use \effcore\fieldset;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        page::get_current()->args_set('back_insert_is_canceled', true);
        if ($items['#expired']->value_get() == null)
            $items['#expired']->value_set(core::datetime_get('+'.core::date_period_w.' second'));
        $fieldset_answers = new fieldset('Answers');
        $form->child_select('fields')->child_insert($fieldset_answers, 'answers');
        for ($i = 0; $i < 10; $i++) {
        # field for answer text
          $c_field_answer_text = new field_text('Text');
          $c_field_answer_text->description_state = 'hidden';
          $c_field_answer_text->build();
          $c_field_answer_text->name_set('answer_text_'.$i);
          $c_field_answer_text->required_set($i == 0);
        # group field to box
          $c_box_answer = new markup('x-widget', ['data-fields-is-inline' => 'true']);
          $c_box_answer    ->child_insert($c_field_answer_text, 'answer_text');
          $fieldset_answers->child_insert($c_box_answer,        'answer_'.$i );
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $back_insert_0 = page::get_current()->args_get('back_insert_0');
    $back_insert_n = page::get_current()->args_get('back_insert_n');
    $entity_name   = page::get_current()->args_get('entity_name'  );
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
          if ($entity->name == 'poll' && !empty($form->_instance)) {
            for ($i = 0; $i < 10; $i++) {
              $c_answer_text = $items['#answer_text_'.$i]->value_get();
              if ($c_answer_text) {
                (new instance('poll_answer', [
                  'id_poll' => $form->_instance->id,
                  'answer'  => $c_answer_text
                ]))->insert();
              }
            }
            url::go($back_insert_0 ?: (url::back_url_get() ?: (
                    $back_insert_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          }
          break;
      }
    }
  }

}}