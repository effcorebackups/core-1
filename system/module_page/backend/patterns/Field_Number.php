<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Number extends Field_Text {

    use Field__Shared;

    const INPUT_MIN_NUMBER = -10000000000;
    const INPUT_MAX_NUMBER = +10000000000;

    public $title = 'Digit';
    public $attributes = [
        'data-type' => 'number'];
    public $element_attributes = [
        'type'     => 'number',
        'name'     => 'number',
        'required' => true,
        'min'      => self::INPUT_MIN_NUMBER,
        'max'      => self::INPUT_MAX_NUMBER,
        'step'     => 1,
        'value'    => 0
    ];

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
            $new_value = str_replace(',', '.', $new_value);
            $old_value = $field->value_get_initial();
            $result = static::validate_required       ($field, $form, $element, $new_value) &&
                      static::validate_minlength      ($field, $form, $element, $new_value) &&
                      static::validate_maxlength      ($field, $form, $element, $new_value) &&
                      static::validate_value          ($field, $form, $element, $new_value) &&
                      static::validate_min            ($field, $form, $element, $new_value) &&
                      static::validate_max            ($field, $form, $element, $new_value) &&
                      static::validate_fractional_part($field, $form, $element, $new_value) &&
                      static::validate_range          ($field, $form, $element, $new_value) &&
                      static::validate_pattern        ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                      static::validate_uniqueness     ($field,                  $new_value, $old_value) : true);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && Security::validate_number($new_value) === false) {
            $field->error_set(new Text(
                'Value of "%%_title" field is not a valid number!', ['title' => (new Text($field->title))->render() ]
            ));
        } else {
            return true;
        }
    }

    static function validate_fractional_part($field, $form, $element, &$new_value) {
        if (strlen($new_value)) {
            $string_step        = Core::format_number($field->step_get() ?: 1, Core::FPART_MAX_LEN);
            $step__fpart_length = Core::fractional_part_length_get($string_step, false);
            $value_fpart_length = Core::fractional_part_length_get($new_value  , false);
            if ($value_fpart_length > Core::FPART_MAX_LEN ||
                $value_fpart_length > $step__fpart_length) {
                $field->error_set(new Text(
                    'Value of "%%_title" field contains a fractional part that is too long!', ['title' => (new Text($field->title))->render() ]
                ));
                return;
            }
        }
        return true;
    }

    static function validate_range($field, $form, $element, &$new_value) {
        if (strlen($new_value)) {
            $string_step = Core::format_number($field->step_get() ?: 1, Core::FPART_MAX_LEN);
            $string__min = Core::format_number($field-> min_get(),      Core::FPART_MAX_LEN);
            $string__max = Core::format_number($field-> max_get(),      Core::FPART_MAX_LEN);
            if (!Security::validate_range($string__min, $string__max, $string_step, $new_value)) {
                $field->error_set(new Text(
                    'Value of "%%_title" field is not within the valid range!', ['title' => (new Text($field->title))->render() ]
                ));
                return;
            }
        }
        return true;
    }

}
