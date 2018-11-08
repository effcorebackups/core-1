<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('develop');
    $module->install();
  }

  static function on_uninstall() {
    $module = module::get('develop');
    $module->uninstall();
  }

  static function on_enable() {
    $module = module::get('develop');
    $module->enable();
  }

  static function on_disable() {
    $module = module::get('develop');
    $module->disable();
  }

}}