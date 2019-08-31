<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\module;
          abstract class events_module {

  static function on_enable($event) {
    $module = module::get('test');
    $module->enable();
  }

  static function on_disable($event) {
    $module = module::get('test');
    $module->disable();
  }

}}