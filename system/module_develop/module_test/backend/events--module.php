<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('test');
    $module->install();
  }

  static function on_uninstall($event) {
    $module = module::get('test');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (module::is_installed('test')) {
       $module = module::get('test');
       $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('test');
    $module->disable();
  }

}}