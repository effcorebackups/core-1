<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Validator_step_Error_register {

    public $break; # null | 'nested' | 'global'
    public $message;

    function run(&$data_validator, $c_dpath_scenario, $c_dpath_value, $c_value, &$c_results) {
        $result = Core::deep_clone($this->message);
        if (is_string($result)) $result = new Text($result);
        if ($result instanceof Text)
            $result->args += ['dpath_scenario' => $c_dpath_scenario, 'dpath_value' => $c_dpath_value];
        if ($this->break === 'global') $c_results['break_global'] = true;
        if ($this->break === 'nested') $c_results['break_nested'][$c_dpath_value.'/'] = $c_dpath_value.'/';
        $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
        $c_results['errors'][$c_dpath_value.'|'.$c_dpath_scenario] = $result;
    }

}
