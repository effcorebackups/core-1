<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\module;
          use \effcore\session;
          use \effcore\user;
          abstract class events_module {

  static function on_install() {
    $module = module::get('user');
    $module->install();
  }

  static function on_start() {
    $session = session::select();
    if ($session &&
        $session->id_user) {
      user::init($session->id_user);
    }
  }

}}