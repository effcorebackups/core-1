<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\event;
          use \effcore\message;
          use \effcore\storage;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form extends \effcore\events_form {

  #####################
  ### form: install ###
  #####################

  static function on_init_install($form, $items) {
    if (!extension_loaded('pdo')) {
      message::insert('PHP PDO extension is not available.', 'warning');
    }
    if (!extension_loaded('pdo_mysql')) {
      $items['storage/is_mysql']->child_select('element')->attribute_insert('disabled', 'disabled');
      message::insert(translation::get('PHP PDO driver for %%_name is not available.', ['name' => 'MySQL']), 'warning');
    }
    if (!extension_loaded('pdo_sqlite')) {
      $items['storage/is_sqlite']->child_select('element')->attribute_insert('disabled', 'disabled');
      message::insert(translation::get('PHP PDO driver for %%_name is not available.', ['name' => 'SQLite']), 'warning');
    }
    $main = storage::get('main');
    if (isset($main->driver)) {
      $form->child_delete('storage');
      $form->child_delete('license_agreement');
      $form->child_delete('button_install');
      message::insert('Installation is not available because storage credentials was setted!', 'warning');
    }
  }

  static function on_validate_install($form, $items) {
    switch ($form->clicked_button_name) {
      case 'install':
        if ($items['storage/is_mysql' ]->checked_get() == false &&
            $items['storage/is_sqlite']->checked_get() == false) {
          $form->error_add('storage/is_mysql/element');
          $form->error_add('storage/is_sqlite/element');
          $form->error_add(null, 'Driver is not selected!');
          return;
        }
        if (count($form->errors) == 0) {
          if ($items['storage/is_mysql']->checked_get()) {
            $test = storage::get('main')->test('mysql', (object)[
              'host_name'  => $items['#host_name']->value_get(),
              'port'       => $items['#port']->value_get(),
              'storage_id' => $items['#storage_id']->value_get(),
              'user_name'  => $items['#user_name']->value_get(),
              'password'   => $items['#password']->value_get()
            ]);
            if ($test !== true) {
              $form->error_add('storage/mysql/storage_id/element');
              $form->error_add('storage/mysql/host_name/element');
              $form->error_add('storage/mysql/port/element');
              $form->error_add('storage/mysql/user_name/element');
              $form->error_add('storage/mysql/password/element');
              $form->error_add(null, translation::get('Storage is not available with these credentials!').br.
                                     translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
            }
          }
          if ($items['storage/is_sqlite']->checked_get()) {
            $test = storage::get('main')->test('sqlite', (object)[
              'file_name' => $items['#file_name']->value_get()
            ]);
            if ($test !== true) {
              $form->error_add('storage/sqlite/file_name/element');
              $form->error_add(null, translation::get('Storage is not available with these credentials!').br.
                                     translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
            }
          }
        }
        break;
    }
  }

  static function on_submit_install($form, $items) {
    switch ($form->clicked_button_name) {
      case 'install':
        if ($items['storage/is_mysql']->checked_get()) {
          $params = new \stdClass;
          $params->driver = 'mysql';
          $params->credentials = new \stdClass;
          $params->credentials->host_name  = $items['#host_name']->value_get();
          $params->credentials->port       = $items['#port']->value_get();
          $params->credentials->storage_id = $items['#storage_id']->value_get();
          $params->credentials->user_name  = $items['#user_name']->value_get();
          $params->credentials->password   = $items['#password']->value_get();
          $params->table_prefix            = $items['#table_prefix']->value_get();
        }
        if ($items['storage/is_sqlite']->checked_get()) {
          $params = new \stdClass;
          $params->driver = 'sqlite';
          $params->credentials = new \stdClass;
          $params->credentials->file_name = $items['#file_name']->value_get();
          $params->table_prefix           = $items['#table_prefix']->value_get();
        }
        storage::get('files')->changes_insert('core', 'insert', 'storages/storage/storage_pdo_sql', $params, false);
        storage::get('files')->changes_insert('core', 'update', 'settings/core/keys', [
          'cron'            => sha1(rand(0, PHP_INT_MAX)),
          'form_validation' => sha1(rand(0, PHP_INT_MAX)),
          'session'         => sha1(rand(0, PHP_INT_MAX))
        ]);
        storage::cache_reset();
        event::start('on_module_install');
        message::insert('Modules was installed.');
        $form->child_delete('storage');
        $form->child_delete('license_agreement');
        $form->child_delete('button_install');
        break;
      case 'to_front':
        url::go('/');
        break;
    }
  }

}}