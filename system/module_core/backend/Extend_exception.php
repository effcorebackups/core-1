<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use effcore\Text_multiline;
use effcore\Text;
use Exception;

class Extend_exception extends Exception {

    protected $ex_message;
    protected $ex_message_args = [];

    function __construct($message = null, $code = 0, $ex_message = null, $ex_message_args = []) {
        $this->ex_message      = $ex_message;
        $this->ex_message_args = $ex_message_args;
        parent::__construct($message, $code);
    }

    function getExMessage() {
        return $this->ex_message;
    }

    function getExMessageArgs() {
        return $this->ex_message_args;
    }

    function getExMessageTextObject() {
        if (is_string($this->ex_message)) return new Text          ($this->ex_message, $this->ex_message_args);
        if (is_array ($this->ex_message)) return new Text_multiline($this->ex_message, $this->ex_message_args);
        return new Text(
            $this->getMessage()
        );
    }

}
