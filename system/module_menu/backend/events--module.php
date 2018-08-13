<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\tabs;
          use \effcore\tree;
          abstract class events_module extends \effcore\events_module {

  static function on_install($module_id = 'menu') {
    return parent::on_install($module_id);
  }

  static function on_start() {
    tree::init();
    tree::build();
    tabs::init();
    tabs::build();
  }

}}