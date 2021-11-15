<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
      if (!frontend::select('page_all__console__develop')) {
           frontend::insert('page_all__console__develop', (object)['check' => 'url', 'where' => 'path', 'match' => '%^(?!/develop/).*$%'], 'styles', [
             'path'       => '/system/module_develop/frontend/develop.cssd?page_id=%%_page_id_context',
             'attributes' => ['rel' => 'stylesheet', 'media' => 'all'],
             'weight'     => -500], 'console_style', 'develop'
           );
      }
    }
  }

}}