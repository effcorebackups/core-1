<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_code {

  public $handler;
  public $args;
  public $is_apply_tokens = true;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    if ($this->is_apply_tokens)
      foreach ($this->args as &$c_arg)
        $c_arg = token::apply($c_arg);
    call_user_func_array($this->handler, [
      'test'     => &$test,
      'scenario' => &$c_scenario,
      'step'     => &$c_step,
      'results'  => &$c_results
    ] + $this->args);
  }

}}