<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_DateTime_local extends Field_DateTime {

    const INPUT_MIN_DATETIME = '0001-01-01 12:00:00';
    const INPUT_MAX_DATETIME = '9999-12-31 09:00:00';

    public $title = 'Local Date/Time';
    public $attributes = [
        'data-type' => 'datetime-local'];
    public $element_attributes = [
        'type'     => 'datetime-local',
        'name'     => 'datetime_local',
        'min'      => self::INPUT_MIN_DATETIME,
        'max'      => self::INPUT_MAX_DATETIME,
        'required' => true,
        'step'     => 1];
    public $is_UTC = true;

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

    function value_get() { # @return: null | string
        $value = Field_Text::value_get();
        if (is_string($value) && Security::validate_datetime  ($value) && $this->is_UTC) return Locale::datetime_loc_to_utc(                             $value );
        if (is_string($value) && Security::validate_T_datetime($value) && $this->is_UTC) return Locale::datetime_loc_to_utc(Core::T_datetime_to_datetime($value));
        if (is_string($value))                                                           return                                                          $value;
        if (is_null  ($value))                                                           return                                                          $value;
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null  ($value) && $this->value_current_if_null !== true)                  return Field_Text::value_set('');
        if (is_null  ($value) && $this->value_current_if_null === true)                  return Field_Text::value_set(Core::datetime_to_T_datetime(Locale::datetime_utc_to_loc    (Core::datetime_get())));
        if (is_string($value) && Security::validate_datetime  ($value) && $this->is_UTC) return Field_Text::value_set(Core::datetime_to_T_datetime(Locale::datetime_utc_to_loc    ($value)));
        if (is_string($value) && Security::validate_T_datetime($value) && $this->is_UTC) return Field_Text::value_set(                             Locale::datetime_T_utc_to_T_loc($value) );
        if (is_string($value))                                                           return Field_Text::value_set(                                                             $value  );
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
