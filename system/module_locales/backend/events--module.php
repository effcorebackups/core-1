<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('locales');
    $module->install();
  }

  static function on_enable() {
    $module = module::get('locales');
    $module->enable();
  }

}}