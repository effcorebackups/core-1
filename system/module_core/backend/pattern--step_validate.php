<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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
      if (in_array($this->where, ['dpath', 'type', 'value'])) {
        $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
        $is_regexp = is_string($this->match) && strlen($this->match) && $this->match[0] === '%';
        $match = token::apply((string)$this->match);
        if ($this->where === 'dpath' && $is_regexp === true                                ) $result = (bool)preg_match($match,    $c_dpath_value);
        if ($this->where === 'dpath' && $is_regexp !== true                                ) $result =                  $match === $c_dpath_value;
        if ($this->where === 'type'  && $is_regexp === true                                ) $result = (bool)preg_match($match,    core::gettype($c_value));
        if ($this->where === 'type'  && $is_regexp !== true                                ) $result =                  $match === core::gettype($c_value);
        if ($this->where === 'value' && $is_regexp === true && is_string($c_value) !== true) $result = false;
        if ($this->where === 'value' && $is_regexp === true && is_string($c_value) === true) $result = (bool)preg_match($match,    $c_value);
        if ($this->where === 'value' && $is_regexp !== true                                ) $result =                  $match === $c_value;
        if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_success/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
        if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_failure/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
      }
    }
    if (strpos($this->check, 'parent_') === 0) {
      if (in_array($this->where, ['dpath', 'type', 'value'])) {
        $depth = (int)substr($this->check, strlen('parent_'));
        if ($depth > 0 && isset($c_results['parents_info'][                                    $depth])) $parent = $c_results['parents_info'][                                    $depth];
        if ($depth < 0 && isset($c_results['parents_info'][count($c_results['parents_info']) + $depth])) $parent = $c_results['parents_info'][count($c_results['parents_info']) + $depth];
        if (isset($parent)) {
          $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
          $is_regexp = is_string($this->match) && strlen($this->match) && $this->match[0] === '%';
          $match = token::apply((string)$this->match);
          if ($this->where === 'dpath' && $is_regexp === true                               ) $result = (bool)preg_match($match,    $c_dpath_value);
          if ($this->where === 'dpath' && $is_regexp !== true                               ) $result =                  $match === $c_dpath_value;
          if ($this->where === 'type'  && $is_regexp === true                               ) $result = (bool)preg_match($match,    core::gettype($parent));
          if ($this->where === 'type'  && $is_regexp !== true                               ) $result =                  $match === core::gettype($parent);
          if ($this->where === 'value' && $is_regexp === true && is_string($parent) !== true) $result = false;
          if ($this->where === 'value' && $is_regexp === true && is_string($parent) === true) $result = (bool)preg_match($match,    $parent);
          if ($this->where === 'value' && $is_regexp !== true                               ) $result =                  $match === $parent;
          if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_success/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
          if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_dpath_in_cycle => $c_step) $c_step->run($data_validator, $c_dpath_scenario.':on_failure/'.$c_dpath_in_cycle, $c_dpath_value, $c_value, $c_results);
        }
      }
    }
  }

}}