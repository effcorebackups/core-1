<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Password extends Field_Text {

    public $title = 'Password';
    public $attributes = ['data-type' => 'password'];
    public $element_attributes = [
        'type'      => 'password',
        'name'      => 'password',
        'required'  => true,
        'minlength' => 5,
        'maxlength' => 255
    ];

    function value_get($return_hash = true) { # @return: null | string | hash(string) | __OTHER_TYPE__ (when "value" in *.data is another type)
        $element = $this->child_select('element');
        $value = $element->attribute_select('value');
        if (is_string($value) && strlen($value) && $return_hash !== false) return User::password_hash($value);
        if (is_string($value) && strlen($value) && $return_hash === false) return                     $value;
        return $value;
    }

}
