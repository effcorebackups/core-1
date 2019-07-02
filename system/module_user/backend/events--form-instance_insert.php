<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\group_access;
          use \effcore\page;
          abstract class events_form_instance_insert {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity_name == 'relation_role_ws_user') {
      $items['#id_role']->is_builded = false;
      $items['#id_role']->disabled['anonymous' ] = 'anonymous';
      $items['#id_role']->disabled['registered'] = 'registered';
      $items['#id_role']->disabled['owner'     ] = 'owner';
      $items['#id_role']->build();
    }
  # access group
    if ($entity->ws_access) {
      $group_access = new group_access();
      $group_access->build();
      $form->child_select('fields')->child_insert(
        $group_access, 'group_access'
      );
    }
  }

}}