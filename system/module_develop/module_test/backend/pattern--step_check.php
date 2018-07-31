<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_check {

  public $where;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
  # check on 'http_code'
    if ($this->where == 'http_code') {
      if (array_key_exists('http_code', $c_results['request']['info']) &&
          $this->match ==               $c_results['request']['info']['http_code']) {
        if (isset($this->on_success)) {
          $c_scenario = $this->on_success;
          $c_step = reset($c_scenario);
          $c_results['is_continue'] = true;
          $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => 'http_code', 'value' => $this->match]);
          $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'success']);
        }
      } else {
        if (isset($this->on_failure)) {
          $c_scenario = $this->on_failure;
          $c_step = reset($c_scenario);
          $c_results['is_continue'] = true;
          $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => 'http_code', 'value' => $this->match]);
          $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'failure']);
        }
      }
    }
  # check on 'subm_errs'
    if ($this->where == 'subm_errs') {
      if (array_key_exists('X-Submit-Errors-Count', $c_results['request']['headers']) &&
          $this->match ==                           $c_results['request']['headers']['X-Submit-Errors-Count']) {
        if (isset($this->on_success)) {
          $c_scenario = $this->on_success;
          $c_step = reset($c_scenario);
          $c_results['is_continue'] = true;
          $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => 'subm_errs', 'value' => $this->match]);
          $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'success']);
        }
      } else {
        if (isset($this->on_failure)) {
          $c_scenario = $this->on_failure;
          $c_step = reset($c_scenario);
          $c_results['is_continue'] = true;
          $c_results['reports'][] = translation::get('checking on "%%_name" = "%%_value"', ['name' => 'subm_errs', 'value' => $this->match]);
          $c_results['reports'][] = translation::get('&ndash; result of checking is = "%%_result"', ['result' => 'failure']);
        }
      }
    }
  }

}}