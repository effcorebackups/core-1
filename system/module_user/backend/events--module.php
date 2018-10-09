<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\module;
          use \effcore\session;
          use \effcore\translation;
          use \effcore\user;
          abstract class events_module {

  static function on_install() {
    $module = module::get('user');
    $module->install();
    $admin = new instance('user', ['nick' => 'admin']);
    if ($admin->select()) {
      $password = dechex(random_int(0x10000000, 0x7fffffff));
      $admin->password_hash = core::hash_password_get($password);
      if ($admin->update()) {
        message::insert(translation::get('Your EMail is: %%_email', ['email' => 'admin@example.com']), 'notice');
        message::insert(translation::get('Your Password is: %%_password', ['password' => $password]), 'notice');
        message::insert(translation::get('Change this EMail and Password after login to the system!'), 'notice');
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