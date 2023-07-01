<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Button_default extends Button {

    public $title = null;
    public $attributes = [
        'tabindex'  => -1,
        'data-type' => 'default',
        'type'      => 'submit',
        'name'      => 'button'];

    function __construct($attributes = [], $weight = 0) {
        parent::__construct(null, $attributes, $weight);
    }

    function build() {
        if (!$this->is_builded)
             $this->is_builded = true;
    }

}
