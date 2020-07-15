<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\field_captcha;
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
    $module = module::get('captcha');
    $module->enable();
  }

  static function on_disable($event) {
    $module = module::get('captcha');
    $module->disable();
  }

  static function on_cron_run($event) {
    field_captcha::captcha_old_cleaning();
  }

}}