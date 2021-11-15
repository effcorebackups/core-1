<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\message;
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('core');
    $module->install();
  }

  static function on_enable($event) {
    if (module::is_installed('core')) {
       $module = module::get('core');
       $module->enable();
    }
  }

  static function on_cron_run($event) {
    message::cleaning();
  }

}}