<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_select_text_direction extends field_select {

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
