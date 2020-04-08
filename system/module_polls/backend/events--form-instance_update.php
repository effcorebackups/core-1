<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\page;
          use \effcore\translation;
          use \effcore\url;
          use \effcore\widget_texts;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        $form->is_redirect_enabled = false;
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
        $widget_answers = new widget_texts;
        $widget_answers->title = 'Answers';
        $widget_answers->item_title = 'Answer';
        $widget_answers->name_complex = 'widget_answers';
        $widget_answers->cform = $form;
        $widget_answers->build();
        $widget_answers->value_set_complex($widget_items, true);
        $form->child_select('fields')->child_insert($widget_answers, 'answers');
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'poll') {
            if (count($items['*widget_answers']->value_get_complex()) < 2) {
              $form->error_set('Group "%%_title" must contain a minimum %%_number items!', ['title' => translation::get($items['*widget_answers']->title), 'number' => 2]);
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'poll') {
        switch ($form->clicked_button->value_get()) {
          case 'update':
            $used_ids = [];
            foreach ($items['*widget_answers']->value_get_complex() as $c_item) {
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
          # reset unactual data (for load new IDs too)
            $form->_answers_rows = null;
            $items['*widget_answers']->items_reset();
            static::on_init($event, $form, $items);
          # ↓↓↓ no break ↓↓↓
          case 'cancel':
          # going back
            $back_update_0 = page::get_current()->args_get('back_update_0');
            $back_update_n = page::get_current()->args_get('back_update_n');
            url::go($back_update_0 ?: (url::back_url_get() ?: (
                    $back_update_n ?: $entity->make_url_for_select_multiple() )));
            break;
        }
      }
    }
  }

}}