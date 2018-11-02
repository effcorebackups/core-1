<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\module;
          abstract class events_module {

  static function on_install($demo_args = []) {
    $module = module::get('demo');
    $module->install();
  }

  static function on_start() {
  }

}}