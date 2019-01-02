<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\module;
          abstract class events_module {

  static function on_enable() {
    $module = module::get('develop');
    $module->enable();
  }

  static function on_disable() {
    $module = module::get('develop');
    $module->disable();
  }

}}