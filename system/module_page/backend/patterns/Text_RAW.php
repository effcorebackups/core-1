<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Text_RAW {

    public $text = '';
    public $weight = +0;

    function __construct($text = '', $weight = +0) {
        if ($text !== '') $this->text = $text;
        $this->weight = $weight;
    }

    function render() {
        return $this->text;
    }

}
