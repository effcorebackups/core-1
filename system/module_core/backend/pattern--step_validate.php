<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_validate {

  public $check;
  public $where;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$data_validator, $c_dpath_scenario, $c_dpath_value, $c_value, &$c_results) {
    if ($this->check === 'current') {
      $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
      $is_regexp = is_string($this->match) && strlen($this->match) && $this->match[0] === '%';
      $match = token::apply((string)$this->match);
      if ($this->where === 'dpath' && $is_regexp === true) $result = (bool)preg_match($match,    $c_dpath_value);
      if ($this->where === 'dpath' && $is_regexp !== true) $result =                  $match === $c_dpath_value;
      if ($this->where === 'type'  && $is_regexp === true) $result = (bool)preg_match($match,    core::gettype($c_value));
      if ($this->where === 'type'  && $is_regexp !== true) $result =                  $match === core::gettype($c_value);
      if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_success/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
      if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_failure/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
    }
  }

}}