<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\instance;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_module {

  static function on_install($demo_args = []) {
    $module = module::get('demo');
    $module->install();
    if (count(storage::get('sql')->errors) == 0) {
      for ($i = 1; $i <= 500; $i++) {
        $c_base_date = new \DateTime('2030-01-01 00:08:20', new \DateTimeZone('UTC'));
        $c_id = $i;
        $c_nick = 'user_'.str_pad((string)$i, 3, '0', STR_PAD_LEFT);
        $c_created = $c_base_date->modify('-'.$i.' second')->format('Y-m-d H:i:s');
        $c_is_even = $i % 2 ? 0 : 1;
        $c_item = (new instance('demo_data', [
          'id'      => $c_id,
          'nick'    => $c_nick,
          'created' => $c_created,
          'is_even' => $c_is_even
        ]))->insert();
      }
    }
  }

  static function on_uninstall() {
    $module = module::get('demo');
    $module->uninstall();
  }

  static function on_enable() {
    $module = module::get('demo');
    $module->enable();
  }

  static function on_disable() {
    $module = module::get('demo');
    $module->disable();
  }

  static function on_start() {
  }

}}