<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\polls {
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('polls');
    $module->install();
  }

  static function on_uninstall($event) {
    $module = module::get('polls');
    $module->uninstall();
  }

  static function on_enable($event) {
    $module = module::get('polls');
    $module->enable();
  }

  static function on_disable($event) {
    $module = module::get('polls');
    $module->disable();
  }

}}