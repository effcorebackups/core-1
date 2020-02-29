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
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_instance_delete {

  static function on_init($event, $form, $items) {
    if (!isset($form->managing_group_id)) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!isset($form->entity_name      )) $form->entity_name       = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get($form->entity_name);
    $groups = entity::groups_managing_get();
    if ($entity) {
      if (isset($groups[$form->managing_group_id])) {
        $form->attribute_insert('data-entity_name', $form->entity_name);
        $form->attribute_insert('data-instance_id', $instance_id);
        $id_keys   = $entity->id_get_real();
        $id_values = explode('+', $instance_id);
        if (count($id_keys  ) ==
            count($id_values)) {
          $form->_instance = new instance($form->entity_name, array_combine($id_keys, $id_values));
          if ($form->_instance->select()) {
            if (!empty($form->_instance->is_embed)) core::send_header_and_exit('access_forbidden');
            $question = new markup('p', [], new text('Delete item of type "%%_type" with ID = "%%_id"?', ['type' => translation::get($entity->title), 'id' => $instance_id]));
            $items['info']->child_insert($question, 'question');
          } else core::send_header_and_exit('page_not_found');
        }   else core::send_header_and_exit('page_not_found');
      }     else core::send_header_and_exit('page_not_found');
    }       else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($event, $form, $items) {
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'delete':
          if (!empty($form->_instance)) {
          # delete action
            $form->_result_delete = $form->_instance->delete();
          # show messages
            if ($form->_result_delete)
                   message::insert(new text('Item of type "%%_type" with ID = "%%_id" was deleted.',     ['type' => translation::get($entity->title), 'id' => $instance_id])         );
              else message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not deleted!', ['type' => translation::get($entity->title), 'id' => $instance_id]), 'error');
          }
        # ↓↓↓ no break ↓↓↓
        case 'cancel':
        # going back
          if (empty(page::get_current()->args_get('back_delete_is_canceled'))) {
            $back_delete_0 = page::get_current()->args_get('back_delete_0');
            $back_delete_n = page::get_current()->args_get('back_delete_n');
            url::go($back_delete_0 ?: (url::back_url_get() ?: (
                    $back_delete_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          }
          break;
      }
    }
  }

}}