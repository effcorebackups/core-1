<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\console;
          use \effcore\field_captcha;
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('captcha');
    $module->install();
  }

  static function on_cron() {
    field_captcha::captcha_cleaning();
    $module = module::get('captcha');
    console::log_add('cron', 'clear', 'Cron job for module %%_name was done.', '-', 0, ['name' => $module->title]);
  }

}}