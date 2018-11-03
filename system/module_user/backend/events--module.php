<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\field;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\module;
          use \effcore\session;
          use \effcore\storage;
          use \effcore\translation;
          use \effcore\user;
          abstract class events_module {

  static function on_install() {
    if (count(storage::get('sql')->errors) == 0) {
      $module = module::get('user');
      $module->install();
      $admin = new instance('user', ['nick' => 'admin']);
      if ($admin->select()) {
        $admin->email = field::request_value_get('email');
        $admin->password_hash = core::hash_password_get(field::request_value_get('password'));
        $admin->update();
      }
    }
  }

  static function on_start() {
    $session = session::select();
    if ($session &&
        $session->nick) {
      user::init($session->nick);
    }
  }

}}