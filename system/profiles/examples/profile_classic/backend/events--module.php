<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\color_preset;
          use \effcore\module;
          abstract class events_module {

  static function on_install($event) {
    $module = module::get('profile_classic');
    $module->install();
  }

  static function on_uninstall($event) {
    $module = module::get('profile_classic');
    $module->uninstall();
  }

  static function on_enable($event) {
    if (module::is_installed('profile_classic')) {color_preset::apply('original_classic');
       $module = module::get('profile_classic');
       $module->enable();
    }
  }

  static function on_disable($event) {
    color_preset::reset();
    $module = module::get('profile_classic');
    $module->disable();
  }

}}