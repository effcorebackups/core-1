<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Injection {

    public $name;
    public $data;

    function __construct($data = null, $name = null) {
        if ($data) $this->data_set($data);
        if ($name) $this->name_set($name);
    }

    function data_get() {return $this->data;}
    function data_set($data) {$this->data = $data;}

    function name_get() {return $this->name;}
    function name_set($name) {$this->name = $name;}

}
