<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_ID_text extends Field_Text {

    const CHARACTERS_ALLOWED = 'a-z0-9_';
    const CHARACTERS_ALLOWED_FOR_DESCRIPTION = '"a-z", "0-9", "_"';

    public $title = 'ID';
    public $attributes = ['data-type' => 'id_text'];
    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'id',
        'required'  => true,
        'maxlength' => 255
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

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value) && !Core::validate_id($new_value)) {
            $field->error_set(new Text_multiline([
                'Field "%%_title" contains an error!',
                'Field value can contain only the next characters: %%_characters'], ['title' => (new Text($field->title))->render(), 'characters' => static::CHARACTERS_ALLOWED_FOR_DESCRIPTION ]
            ));
        } else {
            return true;
        }
    }

}
