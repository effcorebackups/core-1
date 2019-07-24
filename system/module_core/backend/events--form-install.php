<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\core;
          use \effcore\event;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\page;
          use \effcore\storage;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_install {

  static function on_init($event, $form, $items) {
    if (!storage::get('sql')->is_installed()) {
      $items['#password']->value_set(core::password_generate());
    # check for php dependencies
      $embed = module::get_embed();
      $dependencies = [];
      foreach ($embed as $c_module)
        $dependencies += $c_module->dependencies->php ?? [];
      foreach ($dependencies as $c_dependency) {
        if (!extension_loaded($c_dependency)) {
          message::insert(new text('The PHP extension "%%_name" is not available!', ['name' => $c_dependency]), 'error');
          $items['~install']->disabled_set();
        }
      }
    # check opcache
      if (!extension_loaded('Zend OPcache')) {
        message::insert(new text_multiline([
          'The PHP extension "%%_name" is not available!',
          'With it, you can speed up the system from 2-3x and more.'], ['name' => 'Zend OPcache']
        ), 'warning');
      }
    # check php dependencies for storage
      if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_sqlite')) {
        $items['#driver:mysql' ]->disabled_set();
        $items['#driver:sqlite']->disabled_set();
        $items['~install'      ]->disabled_set();
        message::insert(new text('The PHP extension "%%_name" is not available!', ['name' => 'pdo_mysql' ]), 'error');
        message::insert(new text('The PHP extension "%%_name" is not available!', ['name' => 'pdo_sqlite']), 'error');
      } else {
        if (!extension_loaded('pdo_mysql' )) {$items['#driver:mysql' ]->disabled_set(); message::insert(new text('The PHP extension "%%_name" is not available!', ['name' => 'pdo_mysql' ]), 'warning');}
        if (!extension_loaded('pdo_sqlite')) {$items['#driver:sqlite']->disabled_set(); message::insert(new text('The PHP extension "%%_name" is not available!', ['name' => 'pdo_sqlite']), 'warning');}
      }
    } else {
      $form->children_delete();
      core::send_header_and_exit('access_forbidden', null, new text_multiline([
        'Installation is not available because storage credentials was set!',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'install':
        if (!storage::get('sql')->is_installed()) {
          if ($items['#driver:mysql' ]->checked_get() == false &&
              $items['#driver:sqlite']->checked_get() == false) {
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
          storage::get('files')->changes_insert('core', 'update', 'settings/core/keys', [
            'cron'            => core::key_generate(true),
            'form_validation' => core::key_generate(),
            'session'         => core::key_generate(),
            'salt'            => core::key_generate()
          ]);
          $enabled_by_default = module::get_enabled_by_default();
          $embed              = module::get_embed();
          foreach (module::get_all() as $c_module) {
            if (isset($enabled_by_default[$c_module->id]) || 
                isset($embed             [$c_module->id])) {
              event::start('on_module_install', $c_module->id);
              event::start('on_module_enable',  $c_module->id);
            # cancel installation if an error occurred
              if (count(storage::get('sql')->errors) == 0)
                   {message::insert(new text('Module "%%_title" (%%_id) was installed.',     ['title' => translation::get($c_module->title), 'id' => $c_module->id])         );       }
              else {message::insert(new text('Module "%%_title" (%%_id) was not installed!', ['title' => translation::get($c_module->title), 'id' => $c_module->id]), 'error'); break;}
            }
          }
          if (count(storage::get('sql')->errors) == 0) {
            $form->children_delete();
            $link_page_login = (new markup('a', ['href' => '/login', 'target' => 'login'], 'login'))->render();
            message::insert('System was installed.');
            message::insert(new text_multiline([
              'your EMail is — %%_email',
              'your Password is — %%_password',
              'go to page %%_link'], [
              'link'     => $link_page_login,
              'email'    => $items['#email'   ]->value_get(),
              'password' => $items['#password']->value_get(false)]), 'credentials');
            storage::get('files')->changes_insert('core',    'insert', 'storages/storage/sql', $params, false);
            storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', page::get_current()->args_get('lang_code'), false);
            storage::get('files')->changes_insert('page',    'update', 'settings/page/console_visibility', 'not_show');
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