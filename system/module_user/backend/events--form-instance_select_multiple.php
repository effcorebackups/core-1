<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\dir_root;
          use \effcore\entity;
          use \effcore\page;
          abstract class events_form_instance_select_multiple {

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'apply':
        # delete selected users avatar
          if ($entity->name == 'user' && !empty($form->_selected_instances)) {
            foreach ($form->_selected_instances as $c_instance) {
              if (!empty($c_instance->avatar_path)) {
                @unlink(dir_root.$c_instance->avatar_path);
              }
            }
          }
          break;
      }
    }
  }

}}