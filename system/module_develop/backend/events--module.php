<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\access;
          use \effcore\frontend;
          use \effcore\module;
          use \effcore\user;
          abstract class events_module {

  static function on_enable($event) {
    $module = module::get('develop');
    $module->enable();
  }

  static function on_disable($event) {
    $module = module::get('develop');
    $module->disable();
  }

  static function on_start($event) {
    $settings = module::settings_get('page');
    if (($settings->console_visibility == 'show_for_admin' && access::check((object)['roles' => ['admins' => 'admins']])) ||
        ($settings->console_visibility == 'show_for_everyone')) {
      frontend::insert('console', null, 'styles', [
        'file'       => '/system/module_develop/frontend/develop.cssd',
        'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'develop_style', 'develop'
      );
    }
  }

}}