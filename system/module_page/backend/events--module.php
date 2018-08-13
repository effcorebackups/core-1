<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\console;
          use \effcore\form;
          use \effcore\message;
          use \effcore\page;
          abstract class events_module extends \effcore\events_module {

  static function on_install($module_id = 'page') {
    return parent::on_install($module_id);
  }

  static function on_start() {
    return page::find_and_render();
  }

  static function on_cron() {
    form::validation_cache_clean();
    console::log_add('cron', 'clear', 'Cron job was done.', '-');
  }

}}