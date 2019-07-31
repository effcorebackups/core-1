<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\dir_root;
          use \effcore\entity;
          use \effcore\page;
          abstract class events_form_instance_delete {

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'delete':
        if ($entity) {
        # delete user avatar
          if ($entity->name == 'user' && !empty($form->_instance)) {
            if (!empty($form->_instance->avatar_path)) {
              @unlink(dir_root.$form->_instance->avatar_path);
            }
          }
        }
        break;
    }
  }

}}