<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\core;
          use \effcore\field;
          use \effcore\instance;
          use \effcore\module;
          use \effcore\session;
          use \effcore\storage;
          use \effcore\tree;
          use \effcore\user;
          abstract class events_module {

  static function on_install() {
    $module = module::get('user');
    $module->install();
    if (count(storage::get('sql')->errors) == 0) {
      $admin = new instance('user', ['nick' => 'Admin']);
      if ($admin->select()) {
        $admin->password_hash = core::hash_password_get(field::request_value_get('password'));
        $admin->email = field::request_value_get('email');
        $admin->timezone = field::request_value_get('timezone');
        $admin->update();
      }
    }
  }

  static function on_enable() {
    $module = module::get('user');
    $module->enable();
  }

  static function on_start() {
    $session = session::select();
    if ($session &&
        $session->id_user) {
      user::init($session->id_user);
      $user = user::current_get();
      if (isset($user->roles['registered']) &&
                $user->avatar_path) {
        $tree_item = tree::item_select('registered');
        $tree_item->attribute_insert('data-has-avatar', 'yes');
      }
    }
  }

  static function on_cron() {
    session::cleaning();
  }

}}