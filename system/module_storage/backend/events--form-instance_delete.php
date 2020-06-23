<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_delete {

  static function on_init($event, $form, $items) {
    $items['~delete']->disabled_set();
    if (!$form->managing_group_id) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!$form->entity_name      ) $form->entity_name       = page::get_current()->args_get('entity_name');
    if (!$form->instance_id      ) $form->instance_id       = page::get_current()->args_get('instance_id');
    $entity = entity::get($form->entity_name);
    $groups = entity::get_managing_group_ids();
    if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
      if ($entity) {
        $id_keys   = $entity->id_get_real();
        $id_values = explode('+', $form->instance_id);
        if (count($id_keys) ==
            count($id_values)) {
          $conditions = array_combine($id_keys, $id_values);
          $form->_instance = new instance($form->entity_name, $conditions);
          if ($form->_instance->select()) {
            if (empty($form->_instance->is_embed)) {
              $form->attribute_insert('data-entity_name', $form->entity_name);
              $form->attribute_insert('data-instance_id', $form->instance_id);
              $question = new markup('p', [], new text('Delete item of type "%%_type" with ID = "%%_id"?', ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id]));
              $items['info']->child_insert($question, 'question');
              $items['~delete']->disabled_set(false);
            } else $items['info']->child_insert(new markup('p', [], new text('entity is embedded'    )), 'error_message');
          }   else $items['info']->child_insert(new markup('p', [], new text('wrong instance key'    )), 'error_message');
        }     else $items['info']->child_insert(new markup('p', [], new text('wrong instance keys'   )), 'error_message');
      }       else $items['info']->child_insert(new markup('p', [], new text('wrong entity name'     )), 'error_message');
    }         else $items['info']->child_insert(new markup('p', [], new text('wrong management group')), 'error_message');
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'delete':
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'delete':
          if (!empty($form->_instance)) {
          # delete action
            $form->_result = $form->_instance->delete();
          # show messages
            if ($form->is_show_result_message && $form->_result != null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was deleted.',     ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id])         );
            if ($form->is_show_result_message && $form->_result == null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not deleted!', ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id]), 'error');
          }
        # ↓↓↓ no break ↓↓↓
        case 'cancel':
        # going back
          if (empty(page::get_current()->args_get('back_delete_is_canceled'))) {
            $back_delete_0 = page::get_current()->args_get('back_delete_0');
            $back_delete_n = page::get_current()->args_get('back_delete_n');
            url::go($back_delete_0 ?: (url::back_url_get() ?: (
                    $back_delete_n ?: $entity->make_url_for_select_multiple() )));
          }
          break;
      }
    }
  }

}}