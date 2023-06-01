<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_time extends field_text {

    use field__shared;

    const INPUT_MIN_TIME = '00:00:00';
    const INPUT_MAX_TIME = '23:59:59';

    public $title = 'Time';
    public $attributes = ['data-type' => 'time'];
    public $element_attributes = [
        'type'     => 'time',
        'name'     => 'time',
        'required' => true,
        'min'      => self::INPUT_MIN_TIME,
        'max'      => self::INPUT_MAX_TIME,
        'step'     => 1];
    public $value_current_if_null = false;

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $this->value_set(parent::value_get());
        }
    }

    function value_get() { # @return: null | string | __OTHER_TYPE__ (when "value" in *.data is another type)
        $value = parent::value_get();
        if         (core::validate_time($value))
             return core::sanitize_time($value);
        else return $value;
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null($value) || is_string($value)) {
            if ($this->value_current_if_null === true && $value === null) $value = locale::time_utc_to_loc(core::time_get());
            if (core::validate_time($value)) parent::value_set(core::sanitize_time($value));
            else                             parent::value_set(                    $value );
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
            $new_value = request::value_get($name, static::current_number_generate($name), $form->source_get());
            $new_value = strlen($new_value) === 5 ? $new_value.':00' : $new_value;
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
        if (strlen($new_value) && !core::validate_time($new_value)) {
            $field->error_set(
                'Field "%%_title" contains an incorrect time!', ['title' => (new text($field->title))->render() ]
            );
        } else {
            $new_value = core::sanitize_time($new_value);
            return true;
        }
    }

}
