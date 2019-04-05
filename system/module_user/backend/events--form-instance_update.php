<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_nick;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_validate($form, $items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity_name == 'user') {
          if (!$form->has_error()) {
          # test nick
            if (!field_nick::validate_uniqueness(
              $items['#nick'],
              $items['#nick']->value_get(),
              $items['#nick']->value_initial_get()
            )) return;
          }
        }
        break;
    }
  }

}}