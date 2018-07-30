<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_return {

  public $value;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $c_results['reports'][] = translation::get('return = "%%_return"', ['return' => $this->value]);
    $c_results['return'] = $this->value;
  }

}}