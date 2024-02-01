<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Tel extends Field_Text {

    public $title = 'Telephone number';
    public $description = 'Field value should be represented in the international format of telephone numbers.';
    public $attributes = ['data-type' => 'tel'];
    public $element_attributes = [
        'type'      => 'tel',
        'name'      => 'tel',
        'required'  => true,
        'minlength' => 5,
        'maxlength' => 15
    ];

    ###########################
    ### static declarations ###
    ###########################

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && !Security::validate_tel($new_value)) {
            $field->error_set(
                'Value of "%%_title" field is not a valid telephone number!', ['title' => (new Text($field->title))->render() ]
            );
        } else {
            return true;
        }
    }

}
