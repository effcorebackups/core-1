<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Param_from_form {

    public $name;
    public $default;

    function get() {
        if ($this->name) {
            return Request::value_get($this->name, 0, '_POST', $this->default);
        }
    }

}
