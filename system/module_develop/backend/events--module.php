<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\access;
          use \effcore\console;
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
    if (console::visible_mode_get()) {
      frontend::insert('console', null, 'styles', [
        'path'       => '/system/module_develop/frontend/develop.cssd?page_id=%%_page_id_context',
        'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'develop_style', 'develop'
      );
    }
  }

}}