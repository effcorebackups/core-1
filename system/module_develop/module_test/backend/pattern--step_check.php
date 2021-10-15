<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
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
      $is_regexp = is_string($this->match) && $this->match[0] === '%';
      $operand_1 = (string)token::apply($this->where);
      $operand_2 = (string)token::apply($this->match);
      $operand_1_title = $this->check.($this->where ? ': '.$this->where : '');
      $operator_title = $is_regexp ? (new text('matches'))->render() : '=';
      if ($is_regexp) $result = (bool)preg_match($operand_2,    $operand_1);
      else            $result =                  $operand_2 === $operand_1;
      if ($result === true) $c_results['reports'][$dpath.'/on_success_title'] = new text_multiline(['checking on "%%_operand_1" %%_operator "%%_operand_2"', 'real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', 'result of checking is = "%%_result"'], ['operand_1' => $operand_1_title, 'operator' => $operator_title, 'operand_2' => $operand_2, 'operand_real_1' => $operand_1, 'operand_real_2' => $operand_2, 'result' => (new text('success'))->render() ]);
      if ($result !== true) $c_results['reports'][$dpath.'/on_failure_title'] = new text_multiline(['checking on "%%_operand_1" %%_operator "%%_operand_2"', 'real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', 'result of checking is = "%%_result"'], ['operand_1' => $operand_1_title, 'operator' => $operator_title, 'operand_2' => $operand_2, 'operand_real_1' => $operand_1, 'operand_real_2' => $operand_2, 'result' => (new text('failure'))->render() ]);
      if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_step) { $c_step->run($test, $dpath.'/on_success', $c_results); if (array_key_exists('return', $c_results)) return; }
      if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_step) { $c_step->run($test, $dpath.'/on_failure', $c_results); if (array_key_exists('return', $c_results)) return; }
    }
  }

}}