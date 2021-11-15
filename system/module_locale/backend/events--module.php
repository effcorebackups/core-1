<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locale {
          use \effcore\module;
          abstract class events_module {

  static function on_enable($event) {
    $module = module::get('locale');
    $module->enable();
  }

}}