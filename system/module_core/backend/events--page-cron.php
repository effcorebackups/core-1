<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\console;
          use \effcore\core;
          use \effcore\event;
          use \effcore\timer;
          abstract class events_page_cron {

  static function on_cron_run($page) {
    if ($page->get_args('key') === core::key_get('cron')) {
      timer::tap('cron');
      $result = event::start('on_cron');
      timer::tap('cron');
      foreach ($result as $c_handler => $c_result)
        print 'Run: '.$c_handler.br;
        print 'Cron execution time: '.timer::period_get('cron', -1, -2).' sec.';
      console::log_store();
      exit();
    } else {
      core::send_header_and_exit('page_not_found');
    }
  }

}}