<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\message;
          abstract class events_module_update {

  static function on_update_data_before($event, $update) {
    message::insert('Call '.$event->handler.' for update #'.$update->number);
  }

  static function on_update_data_after($event, $update) {
    message::insert('Call '.$event->handler.' for update #'.$update->number);
  }

  # ─────────────────────────────────────────────────────────────────────

  static function on_update_data_1000($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

  static function on_update_data_1001($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

  static function on_update_data_1002($update) {
    message::insert('Call '.$update->handler);
    return true;
  }

}}