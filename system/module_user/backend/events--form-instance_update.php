<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_password;
          use \effcore\file;
          use \effcore\group_access;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\translation;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'role'
      if ($entity->name == 'relation_role_ws_user' && !empty($form->_instance)) {
        $items['#id_role']->is_builded = false;
        $items['#id_role']->disabled['anonymous' ] = 'anonymous';
        $items['#id_role']->disabled['registered'] = 'registered';
        $items['#id_role']->disabled['owner'     ] = 'owner';
        $items['#id_role']->build();
        $items['#id_role']->value_set($form->_instance->id_role);
      }
    # field 'password'
      if ($entity->name == 'user' && !empty($form->_instance)) {
        $field_password_hash_current = new field_password('Current password', '', [], -50);
        $field_password_hash_current->build();
        $field_password_hash_current->name_set('password_hash_current');
        $form->child_select('fields')->child_insert(
          $field_password_hash_current, 'password_hash_current'
        );
      }
    # field 'avatar'
      if ($entity->name == 'user' && !empty($form->_instance)) {
        $items['#avatar']->fixed_name = 'avatar-'.$form->_instance->id;
      }
    # group 'access'
      if (!empty($entity->ws_access) && !empty($form->_instance)) {
        $group_access = new group_access();
        if ($form->_instance->access && is_array(
            $form->_instance->access->roles)) $group_access->roles_set(
            $form->_instance->access->roles);
        $group_access->build();
        $form->child_select('fields')->child_insert(
          $group_access, 'group_access'
        );
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
        # field 'user' + field 'role'
          if ($entity->name == 'relation_role_ws_user') {
            if (!$form->has_error()) {
              $id_user     = $items['#id_user']->value_get        ();
              $id_role_new = $items['#id_role']->value_get        ();
              $id_role_old = $items['#id_role']->value_get_initial();
              if ($id_role_new != $id_role_old) {
                $result = $entity->instances_select(['conditions' => [
                  'id_user_!f' => 'id_user', 'id_user_operator' => '=', 'id_user_!v' => $id_user, 'and',
                  'id_role_!f' => 'id_role', 'id_role_operator' => '=', 'id_role_!v' => $id_role_new],
                  'limit'      => 1]);
                if ($result) {
                  $items['#id_role']->error_set(new text_multiline([
                    'Field "%%_title" contains incorrect value!',
                    'This combination of values is already in use!'], ['title' => translation::get($items['#id_role']->title)]
                  ));
                }
              }
            }
          }
        # field 'role' + field 'permission'
          if ($entity->name == 'relation_role_ws_permission') {
            if (!$form->has_error()) {
              $id_role           = $items['#id_role'      ]->value_get        ();
              $id_permission_new = $items['#id_permission']->value_get        ();
              $id_permission_old = $items['#id_permission']->value_get_initial();
              if ($id_permission_new != $id_permission_old) {
                $result = $entity->instances_select(['conditions' => [
                  'id_role_!f'       => 'id_role',       'id_role_operator'       => '=', 'id_role_!v'       => $id_role, 'and',
                  'id_permission_!f' => 'id_permission', 'id_permission_operator' => '=', 'id_permission_!v' => $id_permission_new],
                  'limit'            => 1]);
                if ($result) {
                  $items['#id_role'      ]->error_set();
                  $items['#id_permission']->error_set(new text_multiline([
                    'Field "%%_title" contains incorrect value!',
                    'This combination of values is already in use!'], ['title' => translation::get($items['#id_permission']->title)]
                  ));
                }
              }
            }
          }
        # field 'new password' + field 'current password'
          if ($entity->name == 'user' && !$form->has_error() && !empty($form->_instance)) {
            if (!hash_equals($form->_instance->password_hash, $items['#password_hash_current']->value_get())) {
              $items['#password_hash_current']->error_set(
                'Field "%%_title" contains incorrect value!', ['title' => translation::get($items['#password_hash_current']->title)]
              );
              return;
            }
            if ($items['#password_hash_current']->value_get() ==
                $items['#password_hash'        ]->value_get()) {
              $items['#password_hash']->error_set(
                'New password must be different from the current password!'
              );
              return;
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
        # field 'avatar'
          if ($entity->name == 'user' && !empty($form->_instance)) {
            page::get_current()->args_set('back_update', '/user/'.$items['#nick']->value_get());
            $avatar_info = $items['#avatar']->pool_files_save();
            if (!empty($avatar_info[0]->path)) {
               $c_file = new file($avatar_info[0]->path);
               $form->_instance->avatar_path = $c_file->path_get_relative(); } else {
               $form->_instance->avatar_path = null;
            }
          }
        # group 'access'
          if (!empty($entity->ws_access) && !empty($form->_instance)) {
            $roles = $items['fields/group_access']->roles_get();
            if ($roles) $form->_instance->access = (object)['roles' => $roles];
            else        $form->_instance->access = null;
          }
          break;
        case 'cancel':
          if ($entity->name == 'user' && !empty($form->_instance)) {
            page::get_current()->args_set('back_cancel', '/user/'.$items['#nick']->value_get());
          }
          break;
      }
    }
  }

}}