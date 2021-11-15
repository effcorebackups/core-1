<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\console;
          use \effcore\core;
          use \effcore\event;
          use \effcore\response;
          use \effcore\storage;
          use \effcore\text_multiline;
          use \effcore\timer;
          use \effcore\user;
          abstract class events_page_cron {

  static function block_markup__cron($page, $args = []) {
    if ($page->args_get('key') === user::key_get('cron')) {
      timer::tap('cron');
      $result = event::start('on_cron_run');
      timer::tap('cron');
      foreach ($result as $c_handler => $c_result)
        print 'Run: '.$c_handler.br;
        print 'Cron execution time: '.timer::period_get('cron', -1, -2).' sec.';
      core::cron_run_register();
      exit();
    } else {
      response::send_header_and_exit('page_not_found', null, new text_multiline([
        'wrong cron key',
        'go to <a href="/">front page</a>'
      ], [], br.br));
    }
  }

}}