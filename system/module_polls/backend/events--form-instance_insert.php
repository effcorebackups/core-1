<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\page;
          use \effcore\translation;
          use \effcore\url;
          use \effcore\widget_texts;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        $form->is_redirect_enabled = false;
        $items['#expired']->value_set(core::datetime_get('+'.core::date_period_w.' second'));
        $widget_answers = new widget_texts;
        $widget_answers->title = 'Answers';
        $widget_answers->item_title = 'Answer';
        $widget_answers->name_complex = 'widget_answers';
        $widget_answers->cform = $form;
        $widget_answers->build();
        $widget_answers->value_set_complex([
          (object)['weight' =>  0, 'id' => 0, 'text' => 'Answer 1'],
          (object)['weight' => -5, 'id' => 0, 'text' => 'Answer 2']], true);
        $form->child_select('fields')->child_insert($widget_answers, 'answers');
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
          if ($entity->name == 'poll' && !empty($form->_instance)) {
            if (count($items['*widget_answers']->value_get_complex()) < 2) {
              $form->error_set('Group "%%_title" should contain a minimum %%_number item%%_plural{number,s}!', ['title' => translation::apply($items['*widget_answers']->title), 'number' => 2]);
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'poll' && !empty($form->_instance)) {
        switch ($form->clicked_button->value_get()) {
          case 'insert':
            foreach ($items['*widget_answers']->value_get_complex() as $c_item) {
              (new instance('poll_answer', [
                'id_poll' => $form->_instance->id,
                'answer'  => $c_item->text,
                'weight'  => $c_item->weight
              ]))->insert();
            }
          # reset unactual data
            $items['*widget_answers']->items_reset();
            static::on_init($event, $form, $items);
          # ↓↓↓ no break ↓↓↓
          case 'cancel':
          # going back
            $back_insert_0 = page::get_current()->args_get('back_insert_0');
            $back_insert_n = page::get_current()->args_get('back_insert_n');
            url::go($back_insert_0 ?: (url::back_url_get() ?: (
                    $back_insert_n ?: $entity->make_url_for_select_multiple() )));
            break;
        }
      }
    }
  }

}}