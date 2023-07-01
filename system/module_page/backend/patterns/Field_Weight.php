<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Weight extends Field_Number {

    const INPUT_MIN_WEIGHT = -1000;
    const INPUT_MAX_WEIGHT = +1000;

    public $title = 'Weight';
    public $attributes = ['data-type' => 'weight'];
    public $element_attributes = [
        'type'     => 'number',
        'name'     => 'weight',
        'required' => true,
        'min'      => self::INPUT_MIN_WEIGHT,
        'max'      => self::INPUT_MAX_WEIGHT,
        'step'     => 1,
        'value'    => 0
    ];

}
