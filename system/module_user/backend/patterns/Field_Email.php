<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Email extends Field_Text {

    public $title = 'EMail address';
    public $attributes = [
        'data-type' => 'email'];
    public $element_attributes = [
        'type'      => 'email',
        'name'      => 'email',
        'required'  => true,
        'maxlength' => 64
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
            $new_value = mb_strtolower($new_value);
            $result = static::validate_required ($field, $form, $element, $new_value) &&
                      static::validate_minlength($field, $form, $element, $new_value) &&
                      static::validate_maxlength($field, $form, $element, $new_value) &&
                      static::validate_pattern  ($field, $form, $element, $new_value) &&
                      static::validate_multiple ($field, $form, $element, $new_value) &&
                      static::validate_values   ($field, $form, $element, $new_value);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function on_validate_after($field, $form, $npath) {
        $element = $field->child_select('element');
        $name = $field->name_get();
        $type = $field->type_get();
        if ($name && $type) {
            if ($field->disabled_get()) return true;
            if ($field->readonly_get()) return true;
            $new_value = Request::value_get($name, static::current_number_generate($name), $form->source_get());
            $new_value = mb_strtolower(         $new_value        );
            $old_value = mb_strtolower($field->value_get_initial());
            if (!$form->has_error() && !empty($field->is_validate_uniqueness))
                 return static::validate_uniqueness($field, $new_value, $old_value);
            else return true;
        }
    }

    static function validate_multiple($field, $form, $element, &$new_value) {
        $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
        if (!$field->multiple_get() && count($multiple_values) > 1) {
            $new_value = array_pop($multiple_values);
            $field->error_set(new Text_multiline([
                'Field "%%_title" does not support multiple values!',
                'Value has been corrected.',
                'Check value again before submit.'], ['title' => (new Text($field->title))->render()]
            ));
        } else {
            return true;
        }
    }

    static function validate_values($field, $form, $element, &$new_value) {
        $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
        foreach ($multiple_values as $c_value) {
            if (Security::validate_email($c_value) === false) {
                $field->error_set(
                    'Value of "%%_title" field is not a valid EMail address!', ['title' => (new Text($field->title))->render() ]
                );
                return;
            }
        }
        return true;
    }

    static function validate_uniqueness($field, $new_value, $old_value = null) {
        $result = $field->value_is_unique_in_storage_sql($new_value);
        # - old_value === '' && new_value NOT found                            | OK    (e.g. registration - EMail does not exist)
        # - old_value === '' && new_value     found                            | ERROR (e.g. registration - EMail already exists)
        # - old_value !== '' && new_value NOT found                            | OK    (e.g. updating profile - EMail does not exist)
        # - old_value !== '' && new_value     found && old_value === new_value | OK    (e.g. updating profile - EMail already exists and it          belong to me)
        # - old_value !== '' && new_value     found && old_value !== new_value | ERROR (e.g. updating profile - EMail already exists and it does not belong to me)
        if ( (strlen($old_value) === 0 && $result instanceof Instance                                                       ) ||
             (strlen($old_value) !== 0 && $result instanceof Instance && $result->{$field->entity_field_name} !== $old_value) ) {
            $field->error_set(
                'User with this EMail address was already registered!'
            );
        } else {
            return true;
        }
    }

}
