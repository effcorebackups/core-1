<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Text extends Field {

    public $title = 'Text';
    public $attributes = ['data-type' => 'text'];
    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'text',
        'required'  => true,
        'maxlength' => 255];

    ###########################
    ### static declarations ###
    ###########################

    static function on_request_value_set($field, $form, $npath) {
        $name = $field->name_get();
        $type = $field->type_get();
        if ($name && $type) {
            if ($field->disabled_get()) return true;
            if ($field->readonly_get()) return true;
            $new_value = Request::value_get($name, static::current_number_generate($name), $form->source_get());
            $field->value_set($new_value);
        }
    }

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
                      static::validate_pattern   ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                      static::validate_uniqueness($field,                  $new_value, $old_value) : true);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function validate_required($field, $form, $element, &$new_value) {
        if ($field->required_get() && strlen($new_value) === 0) {
            $field->error_set(
                'Field "%%_title" cannot be blank!', ['title' => (new Text($field->title))->render() ]
            );
        } else {
            return true;
        }
    }

    static function validate_minlength($field, $form, $element, &$new_value) {
        $minlength = $field->minlength_get();
        if (strlen($new_value) && is_numeric($minlength) && mb_strlen($new_value, 'UTF-8') < $minlength) {
            $field->error_set(new Text_multiline([
                'Field "%%_title" contains an error!',
                'Field value should contain a minimum of %%_number character%%_plural(number|s).'], ['title' => (new Text($field->title))->render(), 'number' => $minlength]
            ));
        } else {
            return true;
        }
    }

    static function validate_maxlength($field, $form, $element, &$new_value) {
        $maxlength = $field->maxlength_get();
        if (strlen($new_value) && is_numeric($maxlength) && mb_strlen($new_value, 'UTF-8') > $maxlength) {
            $new_value = mb_substr($new_value, 0, $maxlength, 'UTF-8');
            $field->error_set(new Text_multiline([
                'Field "%%_title" contains an error!',
                'Field value can contain a maximum of %%_number character%%_plural(number|s).',
                'Value was trimmed to the required length!',
                'Check field again before submit.'], ['title' => (new Text($field->title))->render(), 'number' => $maxlength]
            ));
        } else {
            return true;
        }
    }

    static function validate_pattern($field, $form, $element, &$new_value) {
        $pattern = $field->pattern_get();
        if (strlen($new_value) && $pattern && !preg_match('%'.$pattern.'%', $new_value)) {
            $field->error_set(new Text_multiline([
                'Field "%%_title" contains an error!',
                'Field value does not match the regular expression "%%_expression".'], ['title' => (new Text($field->title))->render(), 'expression' => $pattern]
            ));
        } else {
            return true;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        return true;
    }

    static function validate_uniqueness($field, $new_value, $old_value = null) {
        $result = $field->value_is_unique_in_storage_sql($new_value);
        if ( (strlen($old_value) === 0 && $result instanceof Instance                                                       ) ||  # insert new value
             (strlen($old_value) !== 0 && $result instanceof Instance && $result->{$field->entity_field_name} !== $old_value) ) { # update old value
            $field->error_set(new Text_multiline([
                'Field "%%_title" contains an error!',
                'Previously used value was specified.',
                'Only unique value is allowed.'], ['title' => (new Text($field->title))->render() ]
            ));
        } else {
            return true;
        }
    }

}
