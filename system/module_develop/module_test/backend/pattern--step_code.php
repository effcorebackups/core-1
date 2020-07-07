<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_code {

  public $handler;
  public $args;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    return call_user_func_array($this->handler, $this->args + ['data' => [
      'test'     => &$test,
      'scenario' => &$c_scenario,
      'step'     => &$c_step,
      'results'  => &$c_results
    ]]);
  }

}}