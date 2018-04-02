<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\tree {
          use \effcore\tree;
          use \effcore\tabs;
          use \effcore\entity;
          use \effcore\message;
          use \effcore\translation;
          abstract class events_module extends \effcore\events_module {

  static function on_start() {
    tree::init();
    tree::build();
    tabs::init();
    tabs::build();
  }

  static function on_install($module_id = 'tree') {
    return parent::on_install($module_id);
  }

}}