<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\entity;
          use \effcore\field_email;
          use \effcore\field_nick;
          use \effcore\field_password;
          use \effcore\file;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->get_args('entity_name');
    $instance_id = page::get_current()->get_args('instance_id');
    if ($entity_name == 'user' && !empty($form->_instance)) {
      $field_password_hash_current = new field_password('Current password', '', [], -50);
      $field_password_hash_current->build();
      $field_password_hash_current->name_set('password_hash_current');
      $form->child_select('fields')->child_insert($field_password_hash_current, 'password_hash_current');
      $items['#avatar']->pool_values_init_old_from_storage(
        $form->_instance->avatar_path ? [$form->_instance->avatar_path] : []
      );
    }
  }

  static function on_validate($form, $items) {
    $entity_name = page::get_current()->get_args('entity_name');
    $instance_id = page::get_current()->get_args('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity_name == 'user' && !$form->has_error() && !empty($form->_instance)) {
        # check security
          if (!hash_equals($form->_instance->password_hash, $items['#password_hash_current']->value_get())) {
            $items['#password_hash_current']->error_set(
              'Field "%%_title" contains incorrect value!', ['title' => translation::get($items['#password_hash_current']->title)]
            );
            return;
          }
        # test nick
          if (!field_nick::validate_uniqueness(
            $items['#nick'],
            $items['#nick']->value_get(),
            $items['#nick']->value_initial_get()
          )) return;
        # test email
          if (!field_email::validate_uniqueness(
            $items['#email'],
            $items['#email']->value_get(),
            $items['#email']->value_initial_get()
          )) return;
        # test new password
          if ($items['#password_hash_current']->value_get() ==
              $items['#password_hash'        ]->value_get()) {
            $items['#password_hash']->error_set(
              'New password must be different from the current password!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    $entity_name = page::get_current()->get_args('entity_name');
    $instance_id = page::get_current()->get_args('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if ($entity_name == 'user' && !empty($form->_instance)) {
          page::get_current()->set_args('back_update', '/user/'.$items['#nick']->value_get());
          $avatar_info = $items['#avatar']->pool_files_save();
          if (!empty($avatar_info[0]->path)) {
             $c_file = new file($avatar_info[0]->path);
             $form->_instance->avatar_path = $c_file->path_get_relative(); } else {
             $form->_instance->avatar_path = null;
          }
        }
        break;
      case 'cancel':
        if ($entity_name == 'user' && !empty($form->_instance)) {
          page::get_current()->set_args('back_cancel', '/user/'.$items['#nick']->value_get());
        }
        break;
    }
  }

}}