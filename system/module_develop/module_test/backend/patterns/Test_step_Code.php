<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_step_Code {

    public $handler;
    public $args = [];
    public $is_apply_tokens = true;

    function run(&$test, $dpath, &$c_results) {
        $args = [];
        foreach ($this->args as $c_key => $c_value)
            if ($this->is_apply_tokens && is_string($c_value))
                 $args[$c_key] = Token::apply($c_value);
            else $args[$c_key] =              $c_value;
        $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        $c_results['reports'][$dpath][] = new Text('call "%%_call"', ['call' => $this->handler]);
        call_user_func_array($this->handler, ['test' => &$test, 'dpath' => $dpath.'::'.Core::handler_get_method($this->handler), 'c_results' => &$c_results] + $args);
    }

}
