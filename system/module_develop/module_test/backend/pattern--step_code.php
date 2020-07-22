<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_code {

  public $handler;
  public $args = [];
  public $is_apply_tokens = true;

  function run(&$test, &$c_scenario, &$c_results) {
    $args = [];
    foreach ($this->args as $c_key => $c_value)
      if ($this->is_apply_tokens && is_string($c_value))
           $args[$c_key] = token::apply($c_value);
      else $args[$c_key] =              $c_value;
    $c_results['reports'][] = new text('call "%%_call"', ['call' => $this->handler]);
    call_user_func_array($this->handler, [
      'test'     => &$test,
      'scenario' => &$c_scenario,
      'results'  => &$c_results
    ] + $args);
  }

}}