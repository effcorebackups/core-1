<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Nickname extends Field_Text {

    const CHARACTERS_ALLOWED = 'a-zA-Z0-9_\\-';
    const CHARACTERS_ALLOWED_FOR_DESCRIPTION = '"a-z", "A-Z", "0-9", "_", "-"';

    public $title = 'Nickname';
    public $attributes = [
        'data-type' => 'nickname'];
    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'nickname',
        'required'  => true,
        'minlength' => 4,
        'maxlength' => 32
    ];

    function render_description() {
        $this->description = static::description_prepare($this->description);
        if (!isset($this->description['characters-allowed']))
                   $this->description['characters-allowed'] = new Markup('p', ['data-id' => 'characters-allowed'], new Text('Field value can contain only the next characters: %%_characters', ['characters' => static::CHARACTERS_ALLOWED_FOR_DESCRIPTION]));
        return parent::render_description();
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
            $result = static::validate_required ($field, $form, $element, $new_value) &&
                      static::validate_minlength($field, $form, $element, $new_value) &&
                      static::validate_maxlength($field, $form, $element, $new_value) &&
                      static::validate_value    ($field, $form, $element, $new_value) &&
                      static::validate_pattern  ($field, $form, $element, $new_value);
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
            $old_value = $field->value_get_initial();
            if (!$form->has_error() && !empty($field->is_validate_uniqueness))
                 return static::validate_uniqueness($field, $new_value, $old_value);
            else return true;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && !Security::validate_nickname($new_value)) {
            $field->error_set(new Text(
                'Value of "%%_title" field can contain only the next characters: %%_characters', ['title' => (new Text($field->title))->render(), 'characters' => static::CHARACTERS_ALLOWED_FOR_DESCRIPTION ]
            ));
        } else {
            return true;
        }
    }

    static function validate_uniqueness($field, $new_value, $old_value = null) {
        # - old_value === '' && new_value NOT found                            | OK    (e.g. registration - nickname does not exist)
        # - old_value === '' && new_value     found                            | ERROR (e.g. registration - nickname already exists)
        # - old_value !== '' && new_value NOT found                            | OK    (e.g. updating profile - nickname does not exist)
        # - old_value !== '' && new_value     found && old_value === new_value | OK    (e.g. updating profile - nickname already exists and it          belong to me)
        # - old_value !== '' && new_value     found && old_value !== new_value | ERROR (e.g. updating profile - nickname already exists and it does not belong to me)
        $result = $field->value_is_unique_in_storage_sql($new_value);
        if ( (strlen($old_value) === 0 && $result instanceof Instance                                                       ) ||
             (strlen($old_value) !== 0 && $result instanceof Instance && $result->{$field->entity_field_name} !== $old_value) ) {
            $field->error_set(
                'User with this nickname was already registered!'
            );
        } else {
            return true;
        }
    }

}
