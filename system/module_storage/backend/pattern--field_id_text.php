<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_id_text extends field_text {

  const characters_allowed = 'a-z0-9_';
  const characters_allowed_for_description = '"a-z", "0-9", "_"';

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
              $this->description['characters-allowed'] = new markup('p', ['data-id' => 'characters-allowed'], new text('Field value can contain only the next characters: %%_characters', ['characters' => static::characters_allowed_for_description]));
   return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_id($new_value)) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Field value can contain only the next characters: %%_characters'], ['title' => (new text($field->title))->render(), 'characters' => static::characters_allowed_for_description ]
      ));
    } else {
      return true;
    }
  }

}}