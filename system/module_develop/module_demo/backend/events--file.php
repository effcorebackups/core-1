<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\console;
          abstract class events_file {

  static function process_demotype($type) {
    $result = 'type: '.$type->type.nl;
    $result.= 'call \effcore\modules\demo\events_file::process_demotype'.nl;
    for ($i = 0; $i < 10; $i++) {
      $result.= 'result string: '.$i.nl;
    }
    header('Content-Length: '.strlen($result));
    header('Cache-Control: private, no-cache');
    print $result;
    console::log_store();
    exit();
  }

}}