<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('captcha');
    $module->install();
  }

}}