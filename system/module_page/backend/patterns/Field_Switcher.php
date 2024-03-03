<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Switcher extends Field_Checkbox {

    public $title;
    public $title_position = 'bottom';
    public $attributes = [
        'data-type' => 'switcher'];
    public $element_attributes = [
        'data-type' => 'switcher',
        'type'      => 'checkbox',
        'name'      => 'checkbox',
        'value'     => 'on'
    ];

}
