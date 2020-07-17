<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_transpositions {

  public $id;
  public $quantity = 1;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $rowids = array_keys($this->actions);
    $actions_count = count($this->actions);
    $max = 2 ** $actions_count;
    for ($i = 1; $i < $max; $i++) {
      for ($j = $actions_count - 1; $j >= 0; $j--) {
        if ($i >> $j & 1) {
          $c_rowid = $rowids[$actions_count - 1 - $j];
          $c_results['reports'][] = [
            new text('transposition %%_cur from %%_max (%%_bits)', ['cur' => $i + 1, 'max' => $max, 'bits' => str_pad(decbin($i), $actions_count, '0', STR_PAD_LEFT)]),
            new text('action with rowid = "%%_rowid" will be started', ['rowid' => $c_rowid]) ];
          $this->actions[$c_rowid]->run($test, $this->actions, $c_step, $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
    }
  }

}}