<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\example_decoration {
          use \effcore\module;
          abstract class events_module {

  static function on_enable($event) {
    $module = module::get('example_decoration');
    $module->enable();
  }

  static function on_disable($event) {
    $module = module::get('example_decoration');
    $module->disable();
  }

}}