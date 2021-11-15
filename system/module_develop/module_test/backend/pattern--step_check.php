<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_check {

  public $check;
  public $where;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$test, $dpath, &$c_results) {
    if ($this->check === 'token') {
      $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
      $is_regexp = is_string($this->match) && strlen($this->match) && $this->match[0] === '%';
      $where = token::apply((string)$this->where);
      $match = token::apply((string)$this->match);
      if ($is_regexp) $result = (bool)preg_match($match,    $where);
      else            $result =                  $match === $where;
      if ($result === true) $c_results['reports'][$dpath]['on_success_title'] = new text_multiline(['checking on "%%_operand_1" %%_operator "%%_operand_2"', 'real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', 'result of checking is = "%%_result"'], ['operand_1' => $this->where, 'operand_2' => $this->match, 'operand_real_1' => $where, 'operand_real_2' => $match, 'operator' => $is_regexp ? '≈' : '=', 'result' => (new text('success'))->render() ]);
      if ($result !== true) $c_results['reports'][$dpath]['on_failure_title'] = new text_multiline(['checking on "%%_operand_1" %%_operator "%%_operand_2"', 'real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', 'result of checking is = "%%_result"'], ['operand_1' => $this->where, 'operand_2' => $this->match, 'operand_real_1' => $where, 'operand_real_2' => $match, 'operator' => $is_regexp ? '≈' : '=', 'result' => (new text('failure'))->render() ]);
      if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_dpath_in_cycle => $c_step) { $c_step->run($test, $dpath.':on_success/'.$c_dpath_in_cycle, $c_results); if (array_key_exists('return', $c_results)) return; }
      if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_dpath_in_cycle => $c_step) { $c_step->run($test, $dpath.':on_failure/'.$c_dpath_in_cycle, $c_results); if (array_key_exists('return', $c_results)) return; }
    }
  }

}}