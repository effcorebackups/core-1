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
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->id_get_real();
      $id_values = explode('+', $instance_id);
      if (count($id_keys  ) ==
          count($id_values)) {
        $form->_instance = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($form->_instance->select()) {
          if (!empty($form->_instance->is_embed)) core::send_header_and_exit('access_forbidden');
          $question = new markup('p', [], new text('Delete item of type "%%_name" with ID = "%%_id"?', ['name' => translation::get($entity->title), 'id' => $instance_id]));
          $items['info']->child_insert($question, 'question');
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($event, $form, $items) {
    $back_delete_0 = page::get_current()->args_get('back_delete_0');
    $back_delete_n = page::get_current()->args_get('back_delete_n');
    $entity_name   = page::get_current()->args_get('entity_name'  );
    $instance_id   = page::get_current()->args_get('instance_id'  );
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'delete':
          if (!empty($form->_instance) &&
                     $form->_instance->delete())
               message::insert(new text('Item of type "%%_name" with id = "%%_id" was deleted.',     ['name' => translation::get($entity->title), 'id' => $instance_id])         );
          else message::insert(new text('Item of type "%%_name" with id = "%%_id" was not deleted!', ['name' => translation::get($entity->title), 'id' => $instance_id]), 'error');
          if (empty(page::get_current()->args_get('back_delete_is_canceled'))) {
            url::go($back_delete_0 ?: (url::back_url_get() ?: (
                    $back_delete_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          }
          break;
      }
    }
  }

}}