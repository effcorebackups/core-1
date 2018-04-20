<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\entity;
          use \effcore\message;
          use \effcore\instance;
          use \effcore\translation;
          abstract class events_module extends \effcore\events_module {

  static function on_start() {
  }

  static function on_install($module_id = 'demo') {
    return parent::on_install($module_id);
  }

}}