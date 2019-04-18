<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\core;
          use \effcore\group_access;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    if (!empty($form->_instance->entity_get()->ws_access)) {
      $group_access = new group_access();
      $group_access->checked = unserialize($form->_instance->access);
      $group_access->build();
      $form->child_select('fields')->child_insert(
        $group_access, 'group_access'
      );
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if (!empty($form->_instance->entity_get()->ws_access)) {
          $access = $items['*roles']->values_get();
          $form->_instance->access = serialize(
            core::array_kmap($access)
          );
        }
        break;
    }
  }

}}