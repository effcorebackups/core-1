<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_repeat {

  public $quantity = 1;
  public $actions = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $r_scenario = $this->actions;
    $r_step = reset($r_scenario);
    $r_iteration = 0;
    while ($r_step !== false) {

    # prevention from looping
      if (++$r_iteration > $this->quantity) {
        break;
      }

    # run next step
      $c_results['reports'][] = translation::get('repeat %%_cur from %%_max', ['cur' => $r_iteration, 'max' => $this->quantity]);
      $r_step->run($test, $r_scenario, $r_step, $c_results);
      if (array_key_exists('is_continue', $c_results)) {unset($c_results['is_continue']); continue;}
      if (array_key_exists('return', $c_results)) break;

    # go to the next item
      $r_step = next($r_scenario);
    }
  }

}}