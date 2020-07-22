<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_check {

  public $where;
  public $which;
  public $match;
  public $on_success;
  public $on_failure;

  function run(&$test, $dpath, &$c_results) {
    $result = ($this->where === 'http_code' && array_key_exists('http_code',                  $c_results['response']['info'   ]) && $this->match === (int)$c_results['response']['info'   ]['http_code'                 ]) ||
              ($this->where === 'subm_errs' && array_key_exists('X-Form-Submit-Errors-Count', $c_results['response']['headers']) && $this->match === (int)$c_results['response']['headers']['X-Form-Submit-Errors-Count']) ||
              ($this->where === 'token'     && (string)token::apply($this->which) === (string)$this->match);
    if ($result === true) $c_results['reports'][$dpath.'/on_success_title'] = new text_multiline(['checking on "%%_name" = "%%_value"', 'result of checking is = "%%_result"'], ['name' => $this->where, 'value' => $this->match, 'result' => (new text('success'))->render() ]);
    if ($result !== true) $c_results['reports'][$dpath.'/on_failure_title'] = new text_multiline(['checking on "%%_name" = "%%_value"', 'result of checking is = "%%_result"'], ['name' => $this->where, 'value' => $this->match, 'result' => (new text('failure'))->render() ]);
    if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_step) { $c_step->run($test, $dpath.'/on_success', $c_results); if (array_key_exists('return', $c_results)) return; }
    if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_step) { $c_step->run($test, $dpath.'/on_failure', $c_results); if (array_key_exists('return', $c_results)) return; }
  }

}}