<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('storage');
    $module->install();
  }

  static function on_enable() {
  }

}}