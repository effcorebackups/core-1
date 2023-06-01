<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_range extends field_number {

    const INPUT_MIN_RANGE = 0;
    const INPUT_MAX_RANGE = 100;

    public $title = 'Range';
    public $attributes = ['data-type' => 'range'];
    public $element_attributes = [
        'type'     => 'range',
        'name'     => 'range',
        'required' => true,
        'min'      => self::INPUT_MIN_RANGE,
        'max'      => self::INPUT_MAX_RANGE,
        'step'     => 1,
        'value'    => 0
    ];

}
