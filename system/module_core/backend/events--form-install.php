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
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_install {

  static function on_init($form, $items) {
    if (!storage::is_installed()) {
      $items['#password']->value_set(dechex(random_int(0x10000000, 0x7fffffff)));
      if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_sqlite')) {
        $items['#driver:mysql' ]->disabled_set();
        $items['#driver:sqlite']->disabled_set();
        $items['~install'      ]->disabled_set();
        message::insert(translation::get('The PHP extension "%%_name" is not available!', ['name' => 'pdo_mysql' ]), 'error');
        message::insert(translation::get('The PHP extension "%%_name" is not available!', ['name' => 'pdo_sqlite']), 'error');
      } else {
        if (!extension_loaded('pdo_mysql' )) {$items['#driver:mysql' ]->disabled_set(); message::insert(translation::get('The PHP extension "%%_name" is not available!', ['name' => 'pdo_mysql' ]), 'warning');}
        if (!extension_loaded('pdo_sqlite')) {$items['#driver:sqlite']->disabled_set(); message::insert(translation::get('The PHP extension "%%_name" is not available!', ['name' => 'pdo_sqlite']), 'warning');}
      }
    } else {
      $form->children_delete_all();
      core::send_header_and_exit('access_forbidden', '',
        translation::get('Installation is not available because storage credentials was set!').br.br.
        translation::get('go to <a href="/">front page</a>')
      );

    }
  }

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'install':
        if (!storage::is_installed()) {
          if ($items['#driver:mysql' ]->checked_get() == false &&
              $items['#driver:sqlite']->checked_get() == false) {
            $items['#driver:mysql' ]->error_set();
            $items['#driver:sqlite']->error_set();
            $form->error_set('Driver is not selected!');
            return;
          }
          if (!form::$errors) {
            if ($items['#driver:mysql']->checked_get()) {
              $test = storage::get('sql')->test('mysql', (object)[
                'host'     => $items['#host'            ]->value_get(),
                'port'     => $items['#port'            ]->value_get(),
                'login'    => $items['#storage_login'   ]->value_get(),
                'password' => $items['#storage_password']->value_get(),
                'database' => $items['#database_name'   ]->value_get()
              ]);
              if ($test !== true) {
                $items['#host'            ]->error_set();
                $items['#port'            ]->error_set();
                $items['#storage_login'   ]->error_set();
                $items['#storage_password']->error_set();
                $items['#database_name'   ]->error_set();
                $form->error_set(translation::get('Storage is not available with these credentials!').br.
                                 translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
              }
            }
            if ($items['#driver:sqlite']->checked_get()) {
              $test = storage::get('sql')->test('sqlite', (object)[
                'file_name' => $items['#file_name']->value_get()
              ]);
              if ($test !== true) {
                $items['#file_name']->error_set();
                $form->error_set(translation::get('Storage is not available with these credentials!').br.
                                 translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
              }
            }
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'install':
        if (!storage::is_installed()) {
          if ($items['#driver:mysql']->checked_get()) {
            $params = new \stdClass;
            $params->driver = 'mysql';
            $params->credentials = new \stdClass;
            $params->credentials->host     = $items['#host'            ]->value_get();
            $params->credentials->port     = $items['#port'            ]->value_get();
            $params->credentials->database = $items['#database_name'   ]->value_get();
            $params->credentials->login    = $items['#storage_login'   ]->value_get();
            $params->credentials->password = $items['#storage_password']->value_get();
            $params->table_prefix          = $items['#table_prefix'    ]->value_get();
          }
          if ($items['#driver:sqlite']->checked_get()) {
            $params = new \stdClass;
            $params->driver = 'sqlite';
            $params->credentials = new \stdClass;
            $params->credentials->file_name = $items['#file_name'   ]->value_get();
            $params->table_prefix           = $items['#table_prefix']->value_get();
          }
          storage::get('sql')->init($params->driver, $params->credentials);
          storage::get('files')->changes_insert('core', 'update', 'settings/core/keys', [
            'cron'            => core::key_generate(),
            'form_validation' => core::key_generate(),
            'session'         => core::key_generate(),
            'salt'            => core::key_generate()
          ]);
          $enabled_by_default = module::enabled_by_default_get();
          $embed              = module::embed_get();
          foreach (module::all_get() as $c_module) {
            if (isset($enabled_by_default[$c_module->id]) || 
                isset($embed             [$c_module->id])) {
              event::start('on_module_install', $c_module->id);
              event::start('on_module_enable',  $c_module->id);
            }
          # cancel installation if an error occurred
            if (count(storage::get('sql')->errors) == 0)
              message::insert(translation::get('Module %%_title (%%_id) was installed.', ['title' => $c_module->title, 'id' => $c_module->id]));
            else break;
          }
          if (count(storage::get('sql')->errors) == 0) {
            $form->children_delete_all();
            $link = (new markup('a', ['href' => '/login', 'target' => 'login'], 'login'))->render();
            message::insert(translation::get('System was installed.'));
            message::insert(translation::get('your EMail is — %%_email',       ['email'    => $items['#email'   ]->value_get()]), 'credentials');
            message::insert(translation::get('your Password is — %%_password', ['password' => $items['#password']->value_get()]), 'credentials');
            message::insert(translation::get('go to page %%_link', ['link' => $link]), 'credentials');
            storage::get('files')->changes_insert('core',    'insert', 'storages/storage/sql', $params, false);
            storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', page::current_get()->args_get('lang_code'), false);
            storage::get('files')->changes_insert('page',    'update', 'settings/page/console_display', 'no');
          } else {
            message::insert(
              translation::get('An error occurred during installation!').br.
              translation::get('System was not installed!'), 'error'
            );
          }
        }
        break;
    }
  }

}}