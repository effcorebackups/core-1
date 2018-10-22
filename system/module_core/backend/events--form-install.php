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
          use \effcore\page;
          use \effcore\storage;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_install {

  static function on_init($form, $items) {
    if (!storage::is_installed()) {
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
      $form->child_delete('elements');
      $link = (new markup('a', ['href' => '/'], 'front'))->render();
      message::insert('Installation is not available because storage credentials was set!', 'warning');
      message::insert(translation::get('Go to page: %%_link.', ['link' => $link]), 'warning');
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
          if ($form->total_errors_count_get() == 0) {
            if ($items['#driver:mysql']->checked_get()) {
              $test = storage::get('main')->test('mysql', (object)[
                'host_name'    => $items['#host_name'   ]->value_get(),
                'port'         => $items['#port'        ]->value_get(),
                'user_name'    => $items['#user_name'   ]->value_get(),
                'password'     => $items['#password'    ]->value_get(),
                'storage_name' => $items['#storage_name']->value_get()
              ]);
              if ($test !== true) {
                $items['#host_name'   ]->error_set();
                $items['#port'        ]->error_set();
                $items['#user_name'   ]->error_set();
                $items['#password'    ]->error_set();
                $items['#storage_name']->error_set();
                $form->error_set(translation::get('Storage is not available with these credentials!').br.
                                 translation::get('Message from storage: %%_message', ['message' => strtolower($test['message'])]));
              }
            }
            if ($items['#driver:sqlite']->checked_get()) {
              $test = storage::get('main')->test('sqlite', (object)[
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
            $params->credentials->host_name    = $items['#host_name'   ]->value_get();
            $params->credentials->port         = $items['#port'        ]->value_get();
            $params->credentials->storage_name = $items['#storage_name']->value_get();
            $params->credentials->user_name    = $items['#user_name'   ]->value_get();
            $params->credentials->password     = $items['#password'    ]->value_get();
            $params->table_prefix              = $items['#table_prefix']->value_get();
          }
          if ($items['#driver:sqlite']->checked_get()) {
            $params = new \stdClass;
            $params->driver = 'sqlite';
            $params->credentials = new \stdClass;
            $params->credentials->file_name = $items['#file_name'   ]->value_get();
            $params->table_prefix           = $items['#table_prefix']->value_get();
          }
          storage::get('files')->changes_insert('core', 'insert', 'storages/storage/storage_pdo_sql', $params, false);
          storage::get('files')->changes_insert('core', 'update', 'settings/locales/lang_code', page::current_get()->args_get('lang_code'), false);
          storage::get('files')->changes_insert('core', 'update', 'settings/core/keys', [
            'cron'            => core::key_generate(),
            'form_validation' => core::key_generate(),
            'session'         => core::key_generate(),
            'salt'            => core::key_generate()
          ]);
          storage::cache_reset();
          event::start('on_module_install');
          $form->child_delete('elements');
          $link = (new markup('a', ['href' => '/login'], 'login'))->render();
          message::insert(translation::get('Modules was installed.'));
          message::insert(translation::get('Go to page: %%_link.', ['link' => $link]), 'notice');
        }
        break;
    }
  }

}}