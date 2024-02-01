<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Group_Checkboxes_colors extends Group_Checkboxes {

    public $attributes = [
        'data-type' => 'checkboxes-colors',
        'role'      => 'group'];
    public $field_class = '\\effcore\\Field_Checkbox_color';
    public $field_attributes = [
        'data-type' => 'checkbox-color'
    ];

}
