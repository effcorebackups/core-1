<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\message;
          abstract class events_module_update {

  static function on_update_1000($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

  static function on_update_1001($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

  static function on_update_1002($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

}}