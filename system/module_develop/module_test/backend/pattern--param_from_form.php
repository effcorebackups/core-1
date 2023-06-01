<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class param_from_form {

    public $name;

    function get() {
        if ($this->name) {
            return request::value_get($this->name);
        }
    }

}
