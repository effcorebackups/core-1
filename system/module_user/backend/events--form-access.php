<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_select;
          use \effcore\field_switcher;
          use \effcore\markup;
          abstract class events_form_access {

  static function on_init($form, $items) {
    $role_entity = entity::get('role');
    $role_instances = $role_entity->instances_select();
    $access = null;
    switch ($form->_args['entity_name']) {
      case 'page': $access = $form->_page->access; break;
    }
    foreach ($role_instances as $c_role) {
      $c_switcher = new field_switcher($c_role->title);
      $c_switcher->element_attributes = ['name' => 'access_for_'.$c_role->id, 'value' => 'on'];
      $c_switcher->build();
      $c_switcher->checked_set(isset($access->roles[$c_role->id]));
      $c_switcher->disabled_set(true);
      $items['settings']->child_insert(
        new markup('x-role', [], [
          $c_switcher
        ])
      );
    }
  }

  static function on_submit($form, $items) {
  }

}}