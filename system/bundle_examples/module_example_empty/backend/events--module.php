<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\example_empty {
          use \effcore\module;
          abstract class events_module {

  static function on_install() {
    $module = module::get('example_empty');
    $module->install();
  }

  static function on_uninstall() {
    $module = module::get('example_empty');
    $module->uninstall();
  }

  static function on_enable() {
    $module = module::get('example_empty');
    $module->enable();
  }

  static function on_disable() {
    $module = module::get('example_empty');
    $module->disable();
  }

}}