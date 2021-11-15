<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\text;
          abstract class events_test {

  static function step_code_demo_handler(&$test, $dpath, &$c_results, $param_1, $param_2, $param_3, $current_iteration) {
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_1', 'value' => $param_1]);
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_2', 'value' => $param_2]);
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'param_3', 'value' => $param_3]);
    $reports[] = new text('%%_param = "%%_value"', ['param' => 'current_iteration', 'value' => $current_iteration]);
    $c_results['reports'][$dpath] = $reports;
  }

}}