<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Checkbox_color extends Field_Checkbox {

    public $title = 'Color';
    public $attributes = [
        'data-type' => 'checkbox-color'
    ];

    function color_set($color_id) {
        $colors = Color::get_all();
        $element = $this->child_select('element');
        if (isset($colors[$color_id])) {
            $element->attribute_insert('style', 'background-color: '.($colors[$color_id]->value_hex ?: '#ffffff'));
            $element->attribute_insert('data-value',                          $color_id);
        } else {
            $this->checked_set(false);
            $this->invalid_set(true);
            $this->disabled_set(true);
        }
    }

}
