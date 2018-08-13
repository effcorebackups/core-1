<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_check {

  public $where;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $result = ($this->where == 'http_code' && array_key_exists('http_code', $c_results['request']['info']) &&
               $this->match == $c_results['request']['info']['http_code']) ||
              ($this->where == 'subm_errs' && array_key_exists('X-Submit-Errors-Count', $c_results['request']['headers']) &&
               $this->match == $c_results['request']['headers']['X-Submit-Errors-Count']);
    if ($result) {
      if (isset($this->on_success)) {
        $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => $this->where, 'value' => $this->match]);
        $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'success']);
        foreach ($this->on_success as $c_step) {
          $c_step->run($test, $this->on_success, $c_step, $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
    } else {
      if (isset($this->on_failure)) {
        $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => $this->where, 'value' => $this->match]);
        $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'failure']);
        foreach ($this->on_failure as $c_step) {
          $c_step->run($test, $this->on_success, $c_step, $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
    }
  }

}}