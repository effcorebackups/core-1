<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_select;
          use \effcore\field_switcher;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_form_access {

  static function on_init($form, $items) {
    $role_entity = entity::get('role');
    $role_instances = $role_entity->instances_select([], ['weight desc', 'title']);
    $access = null;
    switch ($form->_args['entity_name']) {
      case 'page': $access = $form->_page->access; break;
    }
    foreach ($role_instances as $c_role) {
      $c_switcher = new field_switcher($c_role->title);
      $c_switcher->build();
      $c_switcher->name_set('access[]');
      $c_switcher->value_set($c_role->id);
      $c_switcher->checked_set(isset($access->roles[$c_role->id]));
      $items['settings']->child_insert(
        new markup('x-role', [], [
          $c_switcher
        ])
      );
    }
  }

  static function on_validate($form, $items) {
  }

  static function on_submit($form, $items) {
    $role_entity = entity::get('role');
    $role_instances = $role_entity->instances_select([], ['weight desc', 'title']);
    foreach ($role_instances as $c_role) {
      if ($items['#access:'.$c_role->id]->checked_get()) {
        message::insert(translation::get('Role %%_id was selected.', ['id' => $c_role->id]));
      }
    }
  }

}}