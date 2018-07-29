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
    if ($this->where == 'http_code' && isset($c_results['request']['info']['http_code']) &&
        $this->match ==                      $c_results['request']['info']['http_code']) {
      if (isset($this->on_success)) {
        $c_scenario = $this->on_success;
        $c_step = reset($c_scenario);
        $c_results['is_continue'] = true;
        $c_results['reports'][] = translation::get('check::on_success');
      }
    } else {
      if (isset($this->on_failure)) {
        $c_scenario = $this->on_failure;
        $c_step = reset($c_scenario);
        $c_results['is_continue'] = true;
        $c_results['reports'][] = translation::get('check::on_failure');
      }
    }
  }

}}