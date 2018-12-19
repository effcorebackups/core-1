<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\field_captcha;
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('captcha');
    $module->install();
  }

  static function on_uninstall() {
    $module = module::get('captcha');
    $module->uninstall();
  }

  static function on_enable() {
    $module = module::get('captcha');
    $module->enable();
  }

  static function on_disable() {
    $module = module::get('captcha');
    $module->disable();
  }

  static function on_cron() {
    field_captcha::captcha_old_cleaning();
  }

}}