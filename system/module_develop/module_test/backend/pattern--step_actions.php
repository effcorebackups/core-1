<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_actions {

  function run(&$test, &$c_scenario, &$c_results) {
    foreach ($this->actions as $c_step) {
      $c_step->run($test, $this->actions, $c_results);
      if (array_key_exists('return', $c_results)) {
        return;
      }
    }
  }

}}