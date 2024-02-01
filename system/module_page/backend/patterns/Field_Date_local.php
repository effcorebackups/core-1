<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Date_local extends Field_Date {

    public $title = 'Local Date';
    public $attributes = ['data-type' => 'date-local'];

    function build() {
        if (!$this->is_builded) {
            Field_Text::build();
            $this->value_set(Field_Text::value_get());
        }
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null  ($value) && $this->value_current_if_null !== true) return Field_Text::value_set('');
        if (is_null  ($value) && $this->value_current_if_null === true) return Field_Text::value_set(Core::date_get(Core::timezone_get_offset_m(Core::timezone_get_client()).' minutes'));
        if (is_string($value))                                          return Field_Text::value_set($value);
    }

}
