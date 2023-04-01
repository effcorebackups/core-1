<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\entity;
          use \effcore\file;
          use \effcore\release;
          use \effcore\text_multiline;
          use \effcore\text;
          abstract class events_form_instance_insert {

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      case 'insert_and_update':
        if ($entity->name === 'release' && !$form->has_error()) {
        # field 'id_project' + field 'build'
          $id_project = $items['#id_project']->value_get();
          $build      = $items['#build'     ]->value_get();
          if (release::select($id_project, $build)) {
            $items['#id_project']->error_set();
            $items['#build']->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'This combination of values is already in use!'], ['title' => (new text($items['#build']->title))->render() ]
            ));
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      case 'insert_and_update':
        if ($entity->name === 'release' && !$form->has_error()) {
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