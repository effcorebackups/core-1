<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Date extends Field_Text {

    use Field__Shared;

    const INPUT_MIN_DATE = '0001-01-01';
    const INPUT_MAX_DATE = '9999-12-31';

    public $title = 'Date';
    public $attributes = ['data-type' => 'date'];
    public $element_attributes = [
        'type'     => 'date',
        'name'     => 'date',
        'required' => true,
        'min'      => self::INPUT_MIN_DATE,
        'max'      => self::INPUT_MAX_DATE];
    public $value_current_if_null = false;

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $this->value_set(parent::value_get());
        }
    }

    function value_set($value) {
        $this->value_set_initial($value);
        if (is_null  ($value) && $this->value_current_if_null !== true) return parent::value_set('');
        if (is_null  ($value) && $this->value_current_if_null === true) return parent::value_set(Core::date_get());
        if (is_string($value))                                          return parent::value_set($value);
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
        if (strlen($new_value) && !Security::validate_date($new_value)) {
            $field->error_set(
                'Value of "%%_title" field is not a valid date!', ['title' => (new Text($field->title))->render() ]
            );
        } else {
            return true;
        }
    }

}
