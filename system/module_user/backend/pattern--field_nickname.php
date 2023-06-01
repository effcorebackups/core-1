<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class field_nickname extends field_text {

    const CHARACTERS_ALLOWED = 'a-zA-Z0-9_\\-';
    const CHARACTERS_ALLOWED_FOR_DESCRIPTION = '"a-z", "A-Z", "0-9", "_", "-"';

    public $title = 'Nickname';
    public $attributes = ['data-type' => 'nickname'];
    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'nickname',
        'required'  => true,
        'minlength' => 4,
        'maxlength' => 32,
    ];

    function render_description() {
        $this->description = static::description_prepare($this->description);
        if (!isset($this->description['characters-allowed']))
                   $this->description['characters-allowed'] = new markup('p', ['data-id' => 'characters-allowed'], new text('Field value can contain only the next characters: %%_characters', ['characters' => static::CHARACTERS_ALLOWED_FOR_DESCRIPTION]));
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
            $new_value = request::value_get($name, static::current_number_generate($name), $form->source_get());
            $result = static::validate_required ($field, $form, $element, $new_value) &&
                      static::validate_minlength($field, $form, $element, $new_value) &&
                      static::validate_maxlength($field, $form, $element, $new_value) &&
                      static::validate_value    ($field, $form, $element, $new_value) &&
                      static::validate_pattern  ($field, $form, $element, $new_value);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function on_validate_phase_2($field, $form, $npath) {
        $element = $field->child_select('element');
        $name = $field->name_get();
        $type = $field->type_get();
        if ($name && $type) {
            if ($field->disabled_get()) return true;
            if ($field->readonly_get()) return true;
            $new_value = request::value_get($name, static::current_number_generate($name), $form->source_get());
            $old_value = $field->value_get_initial();
            if (!$form->has_error() && !empty($field->is_validate_uniqueness))
                 return static::validate_uniqueness($field, $new_value, $old_value);
            else return true;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && !core::validate_nickname($new_value)) {
            $field->error_set(new text_multiline([
                'Field "%%_title" contains an error!',
                'Field value can contain only the next characters: %%_characters'], ['title' => (new text($field->title))->render(), 'characters' => static::CHARACTERS_ALLOWED_FOR_DESCRIPTION ]
            ));
        } else {
            return true;
        }
    }

    static function validate_uniqueness($field, $new_value, $old_value = null) {
        $result = $field->value_is_unique_in_storage_sql($new_value);
        if ( (strlen($old_value) === 0 && $result instanceof instance                                                       ) ||  # insert new nickname (e.g. registration)
             (strlen($old_value) !== 0 && $result instanceof instance && $result->{$field->entity_field_name} !== $old_value) ) { # update old nickname
            $field->error_set(
                'User with this nickname was already registered!'
            );
        } else {
            return true;
        }
    }

}
