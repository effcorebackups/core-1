<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Datetime extends Field_Text {

    use Field__Shared;

    const INPUT_MIN_DATETIME = '0001-01-01 00:00:00';
    const INPUT_MAX_DATETIME = '9999-12-31 00:00:00';

    public $title = 'Date/Time';
    public $attributes = ['data-type' => 'datetime'];
    public $element_attributes = [
        'type'     => 'datetime-local',
        'name'     => 'datetime',
        'min'      => self::INPUT_MIN_DATETIME,
        'max'      => self::INPUT_MAX_DATETIME,
        'required' => true,
        'step'     => 1];
    public $value_current_if_null = false;

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $min = $this->min_get();
            $max = $this->max_get();
            if ($min) $this->min_set(Core::datetime_to_T_datetime($min));
            if ($max) $this->max_set(Core::datetime_to_T_datetime($max));
            $this->value_set(parent::value_get());
        }
    }

    function value_get() { # @return: null | string | __OTHER_TYPE__ (when "value" in *.data is another type)
        $value = parent::value_get();
        if ($value !== null && Core::validate_T_datetime($value)) $value = Core::T_datetime_to_datetime($value);
        return $value;
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null($value) || is_string($value)) {
            if ($value === null && $this->value_current_if_null === true) $value = Core::datetime_get();
            if ($value !== null && Core::validate_datetime($value)) $value = Core::datetime_to_T_datetime($value);
            parent::value_set($value);
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function on_validate($field, $form, $npath) {
        $element = $field->child_select('element');
        $name = $field->name_get();
        $type = $field->type_get();
        if ($name && $type) {
            if ($field->disabled_get()) return true;
            if ($field->readonly_get()) return true;
            $new_value = Request::value_get($name, static::current_number_generate($name), $form->source_get());
            $new_value = strlen($new_value) === 16 ? $new_value.':00' : $new_value;
            $old_value = $field->value_get_initial();
            $result = static::validate_required  ($field, $form, $element, $new_value) &&
                      static::validate_minlength ($field, $form, $element, $new_value) &&
                      static::validate_maxlength ($field, $form, $element, $new_value) &&
                      static::validate_value     ($field, $form, $element, $new_value) &&
                      static::validate_min       ($field, $form, $element, $new_value) &&
                      static::validate_max       ($field, $form, $element, $new_value) &&
                      static::validate_pattern   ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                      static::validate_uniqueness($field,                  $new_value, $old_value) : true);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && !Core::validate_T_datetime($new_value)) {
            $field->error_set(
                'Field "%%_title" contains an incorrect date/time!', ['title' => (new Text($field->title))->render() ]
            );
        } else {
            $new_value = Core::sanitize_T_datetime($new_value);
            return true;
        }
    }

}
