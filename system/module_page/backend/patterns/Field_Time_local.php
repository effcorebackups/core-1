<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Time_local extends Field_Time {

    public $title = 'Local Time';
    public $attributes = [
        'data-type' => 'time-local'];
    public $is_UTC = true;

    function build() {
        if (!$this->is_builded) {
            Field_Text::build();
            $this->value_set(Field_Text::value_get());
        }
    }

    function value_get() { # @return: null | string
        $value = Field_Text::value_get();
        if (is_string($value) && Security::validate_time($value) && $this->is_UTC) return Locale::time_loc_to_utc($value);
        if (is_string($value))                                                     return                         $value;
        if (is_null  ($value))                                                     return                         $value;
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null  ($value) && $this->value_current_if_null !== true)            return Field_Text::value_set('');
        if (is_null  ($value) && $this->value_current_if_null === true)            return Field_Text::value_set(Locale::time_utc_to_loc(Core::time_get()));
        if (is_string($value) && Security::validate_time($value) && $this->is_UTC) return Field_Text::value_set(Locale::time_utc_to_loc($value));
        if (is_string($value))                                                     return Field_Text::value_set(                        $value );
    }

    ###########################
    ### static declarations ###
    ###########################

    static function on_request_value_set($field, $form, $npath) {
        $field->is_UTC = false;
        $result = parent::on_request_value_set($field, $form, $npath);
        $field->is_UTC = true;
        return $result;
    }

    static function on_validate($field, $form, $npath) {
        $field->is_UTC = false;
        $result = parent::on_validate($field, $form, $npath);
        $field->is_UTC = true;
        return $result;
    }

}
