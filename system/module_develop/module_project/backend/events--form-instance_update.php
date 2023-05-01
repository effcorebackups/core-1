<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\entity;
          use \effcore\file;
          abstract class events_form_instance_update {

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity->name === 'project_release' && !$form->has_error()) {
        # field 'hash sum'
          $file = new file($items['#path']->value_get());
          if ($file->is_exists())
               $items['#hash_sum']->value_set($file->hash_get());
          else $items['#hash_sum']->value_set('');
        }
        break;
    }
  }

}}