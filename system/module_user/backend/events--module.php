<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\module;
          use \effcore\session;
          use \effcore\user;
          abstract class events_module {

  static function on_install() {
    $module = module::get('user');
    $module->install();
    $admin = new instance('user', ['nick' => 'admin']);
    if ($admin->select()) {
      $admin->password_hash = core::hash_password_get('12345');
      $admin->update();
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