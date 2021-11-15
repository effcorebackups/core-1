<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_validate_error_register {

  public $message;

  function run(&$data_validator, $c_dpath_scenario, $c_dpath_value, $c_value, &$c_results) {
    $result = core::deep_clone($this->message);
    if (is_string($result)) $result = new text($result);
    if ($result instanceof text) {
      $result->args['dpath_scenario'] = $c_dpath_scenario;
      $result->args['dpath_value'   ] = $c_dpath_value; }
    $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
    $c_results['errors'][$c_dpath_value.'|'.$c_dpath_scenario] = $result;
  }

}}