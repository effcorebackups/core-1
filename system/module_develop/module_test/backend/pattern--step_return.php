<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_return {

  public $value;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $c_results['reports'][] = new text('return = "%%_return"', ['return' => $this->value]);
    $c_results['return'] = $this->value;
  }

}}