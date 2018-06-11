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

  static function on_validate_install($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'install':
        if (empty($values['driver'][0])) {
          $form->error_add('storage/is_mysql/element');
          $form->error_add('storage/is_sqlite/element');
          $form->error_add(null, 'Driver is not selected!');
          return;
        }
        if (count($form->errors) == 0) {
          switch ($values['driver'][0]) {
            case 'sqlite':
              $test = storage::get('main')->test($values['driver'][0], (object)[
                'file_name' => $values['file_name'][0]
              ]);
              if ($test !== true) {
                $form->error_add('storage/sqlite/file_name/element');
                $form->error_add(null, translation::get('Storage is not available with these credentials!').br.
                                       translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
              }
              break;
            case 'mysql':
              $test = storage::get('main')->test($values['driver'][0], (object)[
                'host_name'  => $values['host_name'][0],
                'port'       => $values['port'][0],
                'storage_id' => $values['storage_id'][0],
                'user_name'  => $values['user_name'][0],
                'password'   => $values['password'][0]
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
              break;
          }
        }
        break;
    }
  }

  static function on_submit_install($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'install':
        switch ($values['driver'][0]) {
          case 'sqlite':
            $params = new \stdClass;
            $params->driver = $values['driver'][0];
            $params->credentials = new \stdClass;
            $params->credentials->file_name = $values['file_name'][0];
            $params->table_prefix           = $values['table_prefix'][0];
            break;
          case 'mysql':
            $params = new \stdClass;
            $params->driver = $values['driver'][0];
            $params->credentials = new \stdClass;
            $params->credentials->host_name  = $values['host_name'][0];
            $params->credentials->port       = $values['port'][0];
            $params->credentials->storage_id = $values['storage_id'][0];
            $params->credentials->user_name  = $values['user_name'][0];
            $params->credentials->password   = $values['password'][0];
            $params->table_prefix            = $values['table_prefix'][0];
            break;
        }
        storage::get('files')->changes_register('core', 'insert', 'storages/storage/storage_pdo_sql', $params, false);
        storage::get('files')->changes_register('core', 'update', 'settings/core/keys', [
          'cron'            => sha1(rand(0, PHP_INT_MAX)),
          'form_validation' => sha1(rand(0, PHP_INT_MAX)),
          'session'         => sha1(rand(0, PHP_INT_MAX))
        ]);
        storage::reset_cache();
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