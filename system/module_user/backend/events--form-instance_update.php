<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_password;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_init === false) {
      $entity = entity::get($form->entity_name);
      if ($entity) {
        if ($entity->name === 'relation_role_ws_user' && !empty($form->_instance)) {
        # field 'role'
          $items['#id_role']->is_builded = false;
          $items['#id_role']->disabled['anonymous' ] = 'anonymous';
          $items['#id_role']->disabled['registered'] = 'registered';
          $items['#id_role']->build();
          $items['#id_role']->value_set($form->_instance->id_role);
          $items['#id_role']->disabled_set(
            $form->_instance->id_user === '1' &&
            $form->_instance->id_role === 'admins'
          );
        }
        if ($entity->name === 'user') {
        # field 'password'
          $field_password_hash_current = new field_password('Current password', null, [], -500);
          $field_password_hash_current->build();
          $field_password_hash_current->name_set('password_hash_current');
          $form->child_select('fields')->child_insert(
            $field_password_hash_current, 'password_hash_current'
          );
        }
        if ($entity->name === 'user' && !empty($form->_instance)) {
        # field 'avatar'
          $items['#avatar_path']->fixed_name = 'avatar-'.$form->_instance->id;
        }
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name === 'relation_role_ws_user' && !$form->has_error()) {
          # field 'user' + field 'role'
            $id_user     = $items['#id_user']->value_get        ();
            $id_role_new = $items['#id_role']->value_get        ();
            $id_role_old = $items['#id_role']->value_get_initial();
            if ($id_role_new !== $id_role_old) {
              $result = $entity->instances_select(['conditions' => [
                'id_user_!f'       => 'id_user',
                'id_user_operator' => '=',
                'id_user_!v'       => $id_user,
                'conjunction'      => 'and',
                'id_role_!f'       => 'id_role',
                'id_role_operator' => '=',
                'id_role_!v'       => $id_role_new], 'limit' => 1]);
              if ($result) {
                $items['#id_role']->error_set(new text_multiline([
                  'Field "%%_title" contains an error!',
                  'This combination of values is already in use!'], ['title' => (new text($items['#id_role']->title))->render() ]
                ));
              }
            }
          }
          if ($entity->name === 'relation_role_ws_permission' && !$form->has_error()) {
          # field 'role' + field 'permission'
            $id_role           = $items['#id_role'      ]->value_get        ();
            $id_permission_new = $items['#id_permission']->value_get        ();
            $id_permission_old = $items['#id_permission']->value_get_initial();
            if ($id_permission_new !== $id_permission_old) {
              $result = $entity->instances_select(['conditions' => [
                'id_role_!f'             => 'id_role',
                'id_role_operator'       => '=',
                'id_role_!v'             => $id_role,
                'conjunction'            => 'and',
                'id_permission_!f'       => 'id_permission',
                'id_permission_operator' => '=',
                'id_permission_!v'       => $id_permission_new], 'limit' => 1]);
              if ($result) {
                $items['#id_role'      ]->error_set();
                $items['#id_permission']->error_set(new text_multiline([
                  'Field "%%_title" contains an error!',
                  'This combination of values is already in use!'], ['title' => (new text($items['#id_permission']->title))->render() ]
                ));
              }
            }
          }
          if ($entity->name === 'user' && !$form->has_error() && !empty($form->_instance)) {
          # field 'new password' + field 'current password'
            if (!hash_equals($form->_instance->password_hash, $items['#password_hash_current']->value_get())) {
              $items['#password_hash_current']->error_set(
                'Field "%%_title" contains an incorrect value!', ['title' => (new text($items['#password_hash_current']->title))->render() ]
              );
              return;
            }
            if ($items['#password_hash_current']->value_get() ==
                $items['#password_hash'        ]->value_get()) {
              $items['#password_hash']->error_set(
                'New password should be different from the current password!'
              );
              return;
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
        case 'cancel':
          if ($entity->name === 'user' && page::get_current()->id === 'user_edit') {
            if (!url::back_url_get())
                 url::back_url_set('back', '/user/'.$items['#nickname']->value_get());
          }
          break;
      }
    }
  }

}}