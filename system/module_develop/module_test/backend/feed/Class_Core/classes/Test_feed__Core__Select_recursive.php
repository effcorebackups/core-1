<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_feed__Core__Select_recursive {

    public $id;
    public $children = [];

    function __construct($properties) {
        foreach ($properties as $c_key => $c_value) {
            $this->{$c_key} = $c_value;
        }
    }

}
