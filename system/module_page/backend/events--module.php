<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\form;
          use \effcore\module;
          use \effcore\page;
          abstract class events_module {

  static function on_install() {
    $module = module::get('page');
    $module->install();
  }

  static function on_enable() {
  }

  static function on_start() {
    return page::find_and_render();
  }

  static function on_cron() {
    form::validation_cache_clean();
  }

}}