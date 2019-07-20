<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\group_access;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\translation;
          abstract class events_form_instance_insert {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'role'
      if ($entity->name == 'relation_role_ws_user') {
        $items['#id_role']->is_builded = false;
        $items['#id_role']->disabled['anonymous' ] = 'anonymous';
        $items['#id_role']->disabled['registered'] = 'registered';
        $items['#id_role']->disabled['owner'     ] = 'owner';
        $items['#id_role']->build();
      }
    # group 'access'
      if (!empty($entity->ws_access) && !empty($form->_instance)) {
        $group_access = new group_access();
        $group_access->build();
        $form->child_select('fields')->child_insert(
          $group_access, 'group_access'
        );
      }
    }
  }

  static function on_validate($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
        if ($entity) {
        # field 'user' + field 'role'
          if ($entity->name == 'relation_role_ws_user') {
            $id_user = $items['#id_user']->value_get();
            $id_role = $items['#id_role']->value_get();
            if ($id_user && $id_role) {
              $result = $entity->instances_select(['conditions' => [
                'id_user_!f' => 'id_user', 'id_user_operator' => '=', 'id_user_!v' => $id_user, 'and',
                'id_role_!f' => 'id_role', 'id_role_operator' => '=', 'id_role_!v' => $id_role],
                'limit'      => 1]);
              if ($result) {
                $items['#id_user']->error_set();
                $items['#id_role']->error_set(new text_multiline([
                  'Field "%%_title" contains incorrect value!',
                  'This combination of values is already in use!'], ['title' => translation::get($items['#id_role']->title)]
                ));
              }
            }
          }
        # field 'role' + field 'permission'
          if ($entity->name == 'relation_role_ws_permission') {
            # @todo: make functionality
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      # group 'access'
        if (!empty($entity->ws_access) && !empty($form->_instance)) {
          $roles = $items['fields/group_access']->roles_get();
          if ($roles) $form->_instance->access = (object)['roles' => $roles];
          else        $form->_instance->access = null;
        }
        break;
    }
  }

}}