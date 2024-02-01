<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

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
