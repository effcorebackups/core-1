<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Select_text_direction extends Field_Select {

    public $title = 'Text direction';
    public $attributes = ['data-type' => 'text_direction'];
    public $element_attributes = [
        'name'     => 'text_direction',
        'required' => true
    ];
    public $items = [
        'not_selected' => '- select -',
        'ltr'          => 'left to right (ltr)',
        'rtl'          => 'right to left (rtl)'
    ];

}
