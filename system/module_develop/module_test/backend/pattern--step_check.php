<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_check {

  public $where;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $result = ($this->where == 'http_code' && array_key_exists('http_code', $c_results['response']['info']) &&
               $this->match == $c_results['response']['info']['http_code']) ||
              ($this->where == 'subm_errs' && array_key_exists('X-Form-Submit-Errors-Count', $c_results['response']['headers']) &&
               $this->match == $c_results['response']['headers']['X-Form-Submit-Errors-Count']);
    if ($result) {
      $c_results['reports'][] = new text_multiline([
        'checking on "%%_name" = "%%_value"',
        '&ndash; result of checking is = "%%_result"'], [
        'name'   => $this->where,
        'value'  => $this->match,
        'result' => translation::get('success')]);
      if (isset($this->on_success)) {
        foreach ($this->on_success as $c_step) {
          $c_step->run($test, $this->on_success, $c_step, $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
    } else {
      $c_results['reports'][] = new text_multiline([
        'checking on "%%_name" = "%%_value"',
        '&ndash; result of checking is = "%%_result"'], [
        'name'   => $this->where,
        'value'  => $this->match,
        'result' => translation::get('failure')]);
      if (isset($this->on_failure)) {
        foreach ($this->on_failure as $c_step) {
          $c_step->run($test, $this->on_failure, $c_step, $c_results);
          if (array_key_exists('return', $c_results)) {
            return;
          }
        }
      }
    }
  }

}}