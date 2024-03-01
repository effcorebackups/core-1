<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_message {

    public $type;
    public $value;
    public $from_file = '';
    public $from_line = 0;

    function __construct($type, $value, $file = '', $line = 0) {
        $this->type      = $type;
        $this->value     = $value;
        $this->from_file = $file;
        $this->from_line = $line;
    }

    static function send_dpath($value) {
        $from = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0];
        return new static('dpath', $value, $from['file'], $from['line']);
    }

}
