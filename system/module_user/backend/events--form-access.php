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
    $action_list = new field_select();
    $action_list->element_attributes = ['name' => 'permissions', 'required' => null];
    $action_list->build();
    $action_list->option_insert('- no -', 'not_selected');
    $action_list->option_insert('select', 's');
    $action_list->option_insert('select | update', 'su');
    $action_list->option_insert('select | update | insert', 'sui');
    $action_list->option_insert('select | update | insert | delete', 'suid');
    foreach ($role_instances as $c_role) {
      $items['settings']->child_insert(
        new markup('x-role', [], [$c_role->title, $action_list])
      );
    }
  }

  static function on_submit($form, $items) {
  }

}}