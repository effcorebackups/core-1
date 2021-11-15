<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\poll {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\poll;
          use \effcore\text;
          use \effcore\url;
          use \effcore\widget_texts;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_init === false) {
      $entity = entity::get($form->entity_name);
      if ($entity) {
        if ($entity->name === 'poll') {
          $form->is_redirect_enabled = false;
          $items['#expired']->value_set(core::datetime_get('+'.core::date_period_w.' second'));
          $widget_answers = new widget_texts;
          $widget_answers->title = 'Answers';
          $widget_answers->item_title = 'Answer';
          $widget_answers->name_complex = 'widget_answers';
          $widget_answers->cform = $form;
          $widget_answers->weight = -500;
          $widget_answers->build();
          $widget_answers->value_set_complex([
            (object)['weight' =>  0, 'id' => 0, 'text' => 'Answer 1'],
            (object)['weight' => -5, 'id' => 0, 'text' => 'Answer 2']], true);
          $form->child_select('fields')->child_insert($widget_answers, 'answers');
        }
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
        case 'insert_and_update':
          if ($entity->name === 'poll' && !empty($form->_instance)) {
            if (count($items['*widget_answers']->value_get_complex()) < 2) {
              $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural{number|s}!', ['title' => (new text($items['*widget_answers']->title))->render(), 'number' => 2]);
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name === 'poll' && !empty($form->_instance)) {
        switch ($form->clicked_button->value_get()) {
          case 'insert':
          case 'insert_and_update':
            foreach ($items['*widget_answers']->value_get_complex() as $c_item)
              poll::answer_insert($form->_instance->id, $c_item->text, $c_item->weight);
          # reset not actual data
            $items['*widget_answers']->items_reset();
            static::on_init(null, $form, $items);
          # ↓↓↓ no break ↓↓↓
          case 'cancel':
            url::go(url::back_url_get() ?: $entity->make_url_for_select_multiple());
            break;
        }
      }
    }
  }

}}