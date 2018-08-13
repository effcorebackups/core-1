<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_repeat {

  public $quantity = 1;
  public $actions = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    for ($i = 1; $i <= $this->quantity; $i++) {
      $c_results['reports'][] = translation::get('repeat %%_cur from %%_max', ['cur' => $i, 'max' => $this->quantity]);
      foreach ($this->actions as $c_step) {
        $c_step->run($test, $this->actions, $c_step, $c_results);
        if (array_key_exists('return', $c_results)) {
          return;
        }
      }
    }
  }

}}