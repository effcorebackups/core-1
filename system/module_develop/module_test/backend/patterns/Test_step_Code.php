<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Code {

    public $handler;
    public $args = [];
    public $is_apply_tokens = true;

    function run(&$test, $dpath) {
        $args = [];
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        yield new Text('call "%%_call"', ['call' => $this->handler]);
        foreach ($this->args as $c_key => $c_value)
            if ($this->is_apply_tokens && is_string($c_value))
                 $args[$c_key] = Token::apply($c_value);
            else $args[$c_key] =              $c_value;
        $result = call_user_func_array($this->handler, [
            'test'  => &$test,
            'dpath' => $dpath.'::'.Core::handler_get_method($this->handler)] + $args
        );
        foreach ($result as $c_tick) {
            yield $c_tick;
        }
    }

}
