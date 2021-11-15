<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\project {
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('project');
    $module->install();
  }

  static function on_uninstall($event) {
    $module = module::get('project');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (module::is_installed('project')) {
       $module = module::get('project');
       $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('project');
    $module->disable();
  }

}}