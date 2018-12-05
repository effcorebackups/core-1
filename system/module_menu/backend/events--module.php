<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\module;
          use \effcore\tabs;
          use \effcore\tree;
          abstract class events_module {

  static function on_install() {
    $module = module::get('menu');
    $module->install();
  }

  static function on_enable() {
    $module = module::get('menu');
    $module->enable();
  }

  static function on_start() {
    tree::init();
    tabs::init();
  }

}}