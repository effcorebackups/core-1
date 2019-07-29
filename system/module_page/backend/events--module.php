<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\form;
          use \effcore\module;
          use \effcore\page;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('page');
    $module->install();
  }

  static function on_enable($event) {
    $module = module::get('page');
    $module->enable();
  }

  static function on_start($event) {
    return page::init_current();
  }

  static function on_cron($event) {
    form::validation_tmp_cleaning();
  }

}}