<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\text;
          abstract class events_test {

  static function step_code_demo_handler(&$test, &$c_scenario, &$c_step, &$c_results, $param_1, $param_2, $param_3) {
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_1', 'value' => $param_1]);
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_2', 'value' => $param_2]);
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_3', 'value' => $param_3]);
    $c_results['reports'][] = $reports;
  }

}}