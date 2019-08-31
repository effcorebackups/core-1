<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\module;
          use \effcore\tabs;
          use \effcore\tree;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('menu');
    $module->install();
  }

  static function on_enable($event) {
    $module = module::get('menu');
    $module->enable();
  }

}}