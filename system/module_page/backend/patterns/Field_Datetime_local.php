<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Datetime_local extends Field_Datetime {

    const INPUT_MIN_DATETIME = '0001-01-01 12:00:00';
    const INPUT_MAX_DATETIME = '9999-12-31 09:00:00';

    public $title = 'Local Date/Time';
    public $attributes = ['data-type' => 'datetime-local'];
    public $element_attributes = [
        'type'     => 'datetime-local',
        'name'     => 'datetime_local',
        'min'      => self::INPUT_MIN_DATETIME,
        'max'      => self::INPUT_MAX_DATETIME,
        'required' => true,
        'step'     => 1];
    public $is_utc_conversion = true;

    function build() {
        if (!$this->is_builded) {
            Field_Text::build();
            $min = $this->min_get();
            $max = $this->max_get();
            if ($min) $this->min_set(Core::datetime_to_T_datetime(Locale::datetime_utc_to_loc($min)));
            if ($max) $this->max_set(Core::datetime_to_T_datetime(Locale::datetime_utc_to_loc($max)));
            $this->value_set(Field_Text::value_get());
        }
    }

    function value_get() { # @return: null | string | __OTHER_TYPE__ (when "value" in *.data is another type)
        $value = parent::value_get();
        if ($value !== null && Core::validate_datetime($value) && $this->is_utc_conversion === true) $value = Locale::datetime_loc_to_utc($value);
        return $value;
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null($value) || is_string($value)) {
            if ($value === null && $this->value_current_if_null === true) $value = Core::datetime_get();
            if ($value !== null && Core::validate_datetime($value)) $value = Core::datetime_to_T_datetime($value);
            if ($value !== null && Core::validate_T_datetime($value) && $this->is_utc_conversion === true) $value = Locale::datetime_T_utc_to_T_loc($value);
            parent::value_set($value);
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function on_validate($field, $form, $npath) {
        $field->is_utc_conversion = false;
        $result = parent::on_validate($field, $form, $npath);
        $field->is_utc_conversion = true;
        return $result;
    }

    static function on_request_value_set($field, $form, $npath) {
        $field->is_utc_conversion = false;
        $result = parent::on_request_value_set($field, $form, $npath);
        $field->is_utc_conversion = true;
        return $result;
    }

}
