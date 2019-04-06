<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_email;
          use \effcore\field_nick;
          use \effcore\field_password;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\translation;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity_name == 'user') {
      $field_password_current = new field_password('Current password', '', [], -50);
      $field_password_current->build();
      $field_password_current->name_set('password_current');
      $form->child_select('fields')->child_insert(
        $field_password_current, 'password_current'
      );
    }
  }

  static function on_validate($form, $items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity_name == 'user') {
          if (!$form->has_error()) {
            $id_keys   = $entity->real_id_get();
            $id_values = explode('+', $instance_id);
          # check security
            $test_password = (new instance('user',
              array_combine($id_keys, $id_values))
            )->select();
            if (!hash_equals($test_password->password_hash, $items['#password_current']->value_get())) {
              $items['#password_current']->error_set(
                'Field "%%_title" contains incorrect value!', ['title' => translation::get($items['#password_current']->title)]
              );
              return;
            }
          # test email
            if (!field_email::validate_uniqueness(
              $items['#email'],
              $items['#email']->value_get(),
              $items['#email']->value_initial_get()
            )) return;
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