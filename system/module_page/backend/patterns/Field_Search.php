<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Search extends Field_Text {

    public $title = 'Search';
    public $attributes = ['data-type' => 'search'];
    public $element_attributes = [
        'type'      => 'search',
        'name'      => 'search',
        'required'  => true,
        'minlength' => 5,
        'maxlength' => 255
    ];

}
