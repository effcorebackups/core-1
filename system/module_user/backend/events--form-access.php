<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_select;
          use \effcore\markup;
          abstract class events_form_access {

  static function on_init($form, $items) {
    $role_entity = entity::get('role');
    $role_instances = $role_entity->instances_select();
    $access = null;
    switch ($form->__args['entity_name']) {
      case 'page': $access = $form->__page->access; break;
    }
    foreach ($role_instances as $c_role) {
      $c_action_list = new field_select();
      $c_action_list->element_attributes = ['name' => 'access_for_'.$c_role->id, 'required' => null];
      $c_action_list->title = $c_role->title;
      $c_action_list->build();
      $c_action_list->option_insert('- no -', 'not_selected');
      $c_action_list->option_insert('select', 's');
      $c_action_list->option_insert('select | update', 'su');
      $c_action_list->option_insert('select | update | insert', 'sui');
      $c_action_list->option_insert('select | update | insert | delete', 'suid');
      $items['settings']->child_insert(
        new markup('x-role', [], [
          $c_action_list
        ])
      );
    }
  }

  static function on_submit($form, $items) {
  }

}}