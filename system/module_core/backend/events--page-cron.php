<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\console;
          use \effcore\core;
          use \effcore\event;
          use \effcore\storage;
          use \effcore\timer;
          abstract class events_page_cron {

  static function on_show_block_cron($page) {
    if ($page->args_get('key') === core::key_get('cron')) {
      timer::tap('cron');
      $result = event::start('on_cron_run');
      timer::tap('cron');
      foreach ($result as $c_handler => $c_result)
        print 'Run: '.$c_handler.br;
        print 'Cron execution time: '.timer::period_get('cron', -1, -2).' sec.';
      storage::get('files')->changes_insert('core', 'update', 'settings/core/cron_last_run_date', core::datetime_get());
      console::log_store();
      exit();
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

}}