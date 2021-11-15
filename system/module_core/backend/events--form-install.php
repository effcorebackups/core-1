<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\cache;
          use \effcore\core;
          use \effcore\data;
          use \effcore\event;
          use \effcore\file;
          use \effcore\message;
          use \effcore\module_as_profile;
          use \effcore\module;
          use \effcore\page;
          use \effcore\response;
          use \effcore\storage;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_install {

  static function on_build($event, $form) {
    if (!storage::get('sql')->is_installed()) {
    # profile selection element
      $field_profile_options = module::get_profiles('title');
      core::array_sort_text($field_profile_options);
      $field_profile = $form->child_select('profile')->child_select('profile');
      $field_profile->values = $field_profile_options;
      $field_profile->selected = ['profile_default' => 'profile_default'];
    } else {
      $form->children_delete();
      response::send_header_and_exit('access_forbidden', null, new text_multiline([
        'Installation is not available because storage credentials was set!',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }
  }

  static function on_init($event, $form, $items) {
    $items['#password']->value_set(user::password_generate());
  # check OPCache
    if (!extension_loaded('Zend OPCache')) {
      message::insert(new text_multiline([
        'PHP extension "%%_extension" is not available!',
        'With it, you can speed up the system from 2-3x and more.'], ['extension' => 'Zend OPCache']
      ), 'warning');
    }
  # check the dependencies of each module
    foreach (module::get_enabled_by_default() as $c_module) {
      $c_dependencies_info = $c_module->dependencies_info_get('default');
      foreach ($c_dependencies_info->sys as $c_id => $c_info) if ($c_info->state < 2) message::insert(new text('Module "%%_title" (%%_id) depend from module '  .  'with ID = "%%_dependency_id" with minimal version = "%%_dependency_version"!', ['title' => $c_module->title, 'id' => $c_module->id, 'dependency_id' => $c_id, 'dependency_version' => $c_info->version_min]), 'error');
      foreach ($c_dependencies_info->php as $c_id => $c_info) if ($c_info->state < 2) message::insert(new text('Module "%%_title" (%%_id) depend from PHP extension with ID = "%%_dependency_id" with minimal version = "%%_dependency_version"!', ['title' => $c_module->title, 'id' => $c_module->id, 'dependency_id' => $c_id, 'dependency_version' => $c_info->version_min]), 'error');
      if ($c_dependencies_info->has_dependencies_sys ||
          $c_dependencies_info->has_dependencies_php) {
        $items['~install']->disabled_set();
      }
    }
  # check the dependencies for the storage
    if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_sqlite')) {
      $items['#driver:mysql' ]->disabled_set();
      $items['#driver:sqlite']->disabled_set();
      $items['~install'      ]->disabled_set();
      message::insert(new text('PHP extension "%%_extension" is not available!', ['extension' => 'pdo_mysql' ]), 'error');
      message::insert(new text('PHP extension "%%_extension" is not available!', ['extension' => 'pdo_sqlite']), 'error');
    } else {
      if (!extension_loaded('pdo_mysql' )) {$items['#driver:mysql' ]->disabled_set(); message::insert(new text('PHP extension "%%_extension" is not available!', ['extension' => 'pdo_mysql' ]), 'warning');}
      if (!extension_loaded('pdo_sqlite')) {$items['#driver:sqlite']->disabled_set(); message::insert(new text('PHP extension "%%_extension" is not available!', ['extension' => 'pdo_sqlite']), 'warning');}
    }
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'install':
        if (!storage::get('sql')->is_installed()) {
          if ($items['#driver:mysql' ]->checked_get() === false &&
              $items['#driver:sqlite']->checked_get() === false) {
            $items['#driver:mysql' ]->error_set();
            $items['#driver:sqlite']->error_set();
            $form->error_set('Driver is not selected!');
            return;
          }
          if (!$form->has_error()) {
            if ($items['#driver:mysql']->checked_get()) {
              $test = storage::get('sql')->test('mysql', (object)[
                'host'     => $items['#host'            ]->value_get(),
                'port'     => $items['#port'            ]->value_get(),
                'login'    => $items['#storage_login'   ]->value_get(),
                'password' => $items['#storage_password']->value_get(false),
                'database' => $items['#database_name'   ]->value_get()
              ]);
              if ($test !== true) {
                $items['#host'            ]->error_set();
                $items['#port'            ]->error_set();
                $items['#storage_login'   ]->error_set();
                $items['#storage_password']->error_set(false);
                $items['#database_name'   ]->error_set();
                $form->error_set(new text_multiline([
                  'Storage is not available with these credentials!',
                  'Message from storage: %%_message'], ['message' => strtolower($test['message'])]
                ));
              }
            }
            if ($items['#driver:sqlite']->checked_get()) {
              file::mkdir_if_not_exists(data::directory);
              $test = storage::get('sql')->test('sqlite', (object)[
                'file_name' => $items['#file_name']->value_get()
              ]);
              if ($test !== true) {
                $items['#file_name']->error_set();
                $form->error_set(new text_multiline([
                  'Storage is not available with these credentials!',
                  'Message from storage: %%_message'], ['message' => strtolower($test['message'])]
                ));
              }
            }
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'install':
        if (!storage::get('sql')->is_installed()) {
          if ($items['#driver:mysql']->checked_get()) {
            $params = new \stdClass;
            $params->driver = 'mysql';
            $params->credentials = new \stdClass;
            $params->credentials->host     = $items['#host'            ]->value_get();
            $params->credentials->port     = $items['#port'            ]->value_get();
            $params->credentials->database = $items['#database_name'   ]->value_get();
            $params->credentials->login    = $items['#storage_login'   ]->value_get();
            $params->credentials->password = $items['#storage_password']->value_get(false);
            $params->table_prefix          = $items['#table_prefix'    ]->value_get();
          }
          if ($items['#driver:sqlite']->checked_get()) {
            $params = new \stdClass;
            $params->driver = 'sqlite';
            $params->credentials = new \stdClass;
            $params->credentials->file_name = $items['#file_name'   ]->value_get();
            $params->table_prefix           = $items['#table_prefix']->value_get();
          }
          storage::get('sql')->init(
            $params->driver,
            $params->credentials,
            $params->table_prefix
          );
          if (!user::keys_install()) {
            return;
          }
        # prepare data about modules which will be installed
          $enabled = module::get_enabled_by_default();
          $modules_to_install = [];
          $modules_to_include = [];
          core::array_sort_by_property($enabled, 'deploy_weight');
          foreach ($enabled as $c_module) {
            if ($c_module instanceof module_as_profile &&
                $c_module->id !== $items['#profile']->value_get()) continue;
            $modules_to_install[$c_module->id] = $c_module;
            $modules_to_include[$c_module->id] = $c_module->path;
          }
        # installation process
          cache::update_global($modules_to_include);
          foreach ($modules_to_install as $c_module) {
            event::start('on_module_install', $c_module->id);
            event::start('on_module_enable',  $c_module->id);
          # cancel installation and show message if module was not installed
            if (count(storage::get('sql')->errors) !== 0) {
              message::insert(new text('Module "%%_title" (%%_id) was not installed!', ['title' => (new text($c_module->title))->render(), 'id' => $c_module->id]), 'error');
              break;
            }
          }
        # save the result if there are no errors
          if (count(storage::get('sql')->errors) === 0) {
            storage::get('data')->changes_insert('core',   'insert', 'storages/storage/sql', $params, false);
            storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', page::get_current()->args_get('lang_code'));
            $form->children_delete();
            message::insert('System was installed.');
            message::insert(new text_multiline([
              'Your Email is: %%_email',
              'Your password is: %%_password'], [
              'email'    => $items['#email'   ]->value_get(),
              'password' => $items['#password']->value_get(false)]), 'credentials');
            url::go('/login');
          } else {
            message::insert(new text_multiline([
              'An error occurred during installation!',
              'System was not installed!']), 'error'
            );
          }
        }
        break;
    }
  }

}}