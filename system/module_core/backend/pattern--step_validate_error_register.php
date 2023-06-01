<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class step_validate_error_register {

    public $break; # null | 'nested' | 'global'
    public $message;

    function run(&$data_validator, $c_dpath_scenario, $c_dpath_value, $c_value, &$c_results) {
        $result = core::deep_clone($this->message);
        if (is_string($result)) $result = new text($result);
        if ($result instanceof text)
            $result->args += ['dpath_scenario' => $c_dpath_scenario, 'dpath_value' => $c_dpath_value];
        if ($this->break === 'global') $c_results['break_global'] = true;
        if ($this->break === 'nested') $c_results['break_nested'][$c_dpath_value.'/'] = $c_dpath_value.'/';
        $c_results['trace_info'][$c_dpath_value][] = $c_dpath_scenario;
        $c_results['errors'][$c_dpath_value.'|'.$c_dpath_scenario] = $result;
    }

}
