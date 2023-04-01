<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\message;
          use \effcore\modules\storage\events_form_instance_insert as storage_events_form_instance_insert;
          use \effcore\page;
          use \effcore\form_plugin;
          use \effcore\text_multiline;
          use \effcore\text;
          abstract class events_form_instance_insert {

  static function on_build($event, $form) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'relation_role_ws_user') {
      # field 'role'
        $form->child_select('fields')->child_select('id_role')->disabled['anonymous' ] = 'anonymous';
        $form->child_select('fields')->child_select('id_role')->disabled['registered'] = 'registered';
      }
      if ($entity->name === 'feedback' && page::get_current()->id !== 'instance_insert') {
      # field 'captcha', button 'cancel', button 'insert'
        $captcha = new form_plugin('field_captcha', [], [], -500);
        $form->child_select('fields')->child_insert($captcha, 'captcha');
        $form->child_delete('button_cancel');
        $form->child_select('button_insert')->title = 'send';
      }
    }
  }

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'feedback' && page::get_current()->id !== 'instance_insert') {
        $form->is_show_result_message = false;
        $form->is_redirect_enabled    = false;
        $items['#name'   ]->value_set('');
        $items['#email'  ]->value_set('');
        $items['#message']->value_set('');
        $items['#captcha']->value_set('');
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      case 'insert_and_update':
        if ($entity->name === 'relation_role_ws_user' && !$form->has_error()) {
        # field 'user' + field 'role'
          $id_user = $items['#id_user']->value_get();
          $id_role = $items['#id_role']->value_get();
          $result = $entity->instances_select(['conditions' => [
            'id_user_!f'       => 'id_user',
            'id_user_operator' => '=',
            'id_user_!v'       => $id_user,
            'conjunction'      => 'and',
            'id_role_!f'       => 'id_role',
            'id_role_operator' => '=',
            'id_role_!v'       => $id_role], 'limit' => 1]);
          if ($result) {
            $items['#id_user']->error_set();
            $items['#id_role']->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'This combination of values is already in use!'], ['title' => (new text($items['#id_role']->title))->render() ]
            ));
          }
        }
        if ($entity->name === 'relation_role_ws_permission' && !$form->has_error()) {
        # field 'role' + field 'permission'
          $id_role       = $items['#id_role'      ]->value_get();
          $id_permission = $items['#id_permission']->value_get();
          $result = $entity->instances_select(['conditions' => [
            'id_role_!f'             => 'id_role',
            'id_role_operator'       => '=',
            'id_role_!v'             => $id_role,
            'conjunction'            => 'and',
            'id_permission_!f'       => 'id_permission',
            'id_permission_operator' => '=',
            'id_permission_!v'       => $id_permission], 'limit' => 1]);
          if ($result) {
            $items['#id_role'      ]->error_set();
            $items['#id_permission']->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'This combination of values is already in use!'], ['title' => (new text($items['#id_permission']->title))->render() ]
            ));
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      case 'insert_and_update':
      # feedback
        if ($entity->name === 'feedback' && page::get_current()->id !== 'instance_insert') {
          message::insert(new text('Feedback with ID = "%%_id" has been sent.', ['id' => implode('+', $form->_instance->values_id_get()) ]));
          static::on_init(null, $form, $items);
        }
        break;
    }
  }

}}