<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_transpositions {

  public $action_before;
  public $actions = [];
  public $action_after;

  function run(&$test, $dpath, &$c_results) {
    $rowids = array_keys($this->actions);
    $actions_count = count($this->actions);
    $max = 2 ** $actions_count;
    for ($i = 1; $i < $max; $i++) {
      $c_bits = str_pad(decbin($i), $actions_count, '0', STR_PAD_LEFT);
      $c_total_state = str_replace(['0', '1'], ['□', '▣'], $c_bits).' | '.$c_bits;
      $c_results['reports'][$dpath.'/'.$i.'/title_line'] = new text('=================================');
      $c_results['reports'][$dpath.'/'.$i.'/title'     ] = new text('transposition %%_cur from %%_max (%%_state)', ['cur' => $i + 1, 'max' => $max, 'state' => $c_total_state]);
      for ($j = $actions_count - 1; $j >= 0; $j--) {
        $c_rowid = $rowids[$actions_count - 1 - $j];
        $c_state = $i >> $j & 1;
        token::insert('test_step_transpositions_is_active_'.$c_rowid,
                   '%%_test_step_transpositions_is_active_'.$c_rowid, 'text', $c_state, null, 'test');
      }
      if ($this->action_before) {
        $c_results['reports'][$dpath.'/'.$i.'/action_before_title'] = new text('action "%%_name" will be started', ['name' => 'action_before']);
        $this->action_before->run($test, $dpath.'/'.$i.'/action_before', $c_results);
        if (array_key_exists('return', $c_results)) {
          return;
        }
      }
      for ($j = $actions_count - 1; $j >= 0; $j--) {
        if ($i >> $j & 1) {
          $c_rowid = $rowids[$actions_count - 1 - $j];
          $c_results['reports'][$dpath.'/'.$i.'/'.$j.'/actions_title'] = new text('action with rowid = "%%_rowid" will be started', ['rowid' => $c_rowid]);
          $this->actions[$c_rowid]->run($test, $dpath.'/'.$i.'/'.$j.'/actions', $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
      if ($this->action_after) {
        $c_results['reports'][$dpath.'/'.$i.'/action_after_title'] = new text('action "%%_name" will be started', ['name' => 'action_after']);
        $this->action_after->run($test, $dpath.'/'.$i.'/action_after', $c_results);
        if (array_key_exists('return', $c_results)) {
          return;
        }
      }
    }
  }

}}