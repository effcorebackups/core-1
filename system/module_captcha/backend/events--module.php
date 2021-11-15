<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\captcha;
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('captcha');
    $module->install();
  }

  static function on_uninstall($event) {
    $module = module::get('captcha');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (module::is_installed('captcha')) {
       $module = module::get('captcha');
       $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('captcha');
    $module->disable();
  }

  static function on_cron_run($event) {
    captcha::cleaning();
  }

}}