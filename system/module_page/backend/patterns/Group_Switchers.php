<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Group_Switchers extends Group_Checkboxes {

    public $attributes = [
        'data-type' => 'switchers',
        'role'      => 'group'];
    public $element_attributes = [
        'data-type' => 'switcher'];
    public $field_attributes = [
        'data-type' => 'switcher'
    ];

}
