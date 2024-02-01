<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Checkbox_color extends Field_Checkbox {

    public $title = 'Color';
    public $attributes = [
        'data-type'      => 'checkbox-color',
        'data-hex-value' => '#ffffff'
    ];

    function build($reset = false) {
        if (!$this->is_builded || $reset) {
            parent::build();
            $element = $this->child_select('element');
            $element->attribute_insert('style', 'background-color: '.$this->attribute_select('data-hex-value'));
            $this->is_builded = true;
        }
    }

    function color_set($hex_value) {
        $this->attributes['data-hex-value'] = $hex_value;
        $this->build(true);
    }

}
