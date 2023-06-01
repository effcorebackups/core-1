<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_select_logic extends field_select {

    public $title = 'Logic';
    public $attributes = ['data-type' => 'logic'];
    public $element_attributes = [
        'name'     => 'logic',
        'required' => true
    ];
    public $items = [
        'not_selected' => '- select -',
        '0'            => 'no',
        '1'            => 'yes'
    ];

    function value_set($value) {
        if ($value ===  ''         ) parent::value_set('');
        if ($value === null        ) parent::value_set('');
        if ($value === false       ) parent::value_set('0');
        if ($value === true        ) parent::value_set('1');
        if ($value ===   0         ) parent::value_set('0');
        if ($value ===   1         ) parent::value_set('1');
        if ($value ===  '0'        ) parent::value_set('0');
        if ($value ===  '1'        ) parent::value_set('1');
        if ($value === ['0' => '' ]) parent::value_set('');
        if ($value === ['0' => '0']) parent::value_set('0');
        if ($value === ['0' => '1']) parent::value_set('1');
        if ($value === ['1' => '1']) parent::value_set('1');
    }

}
