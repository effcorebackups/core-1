<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_id_text extends field_text {

  const allowed_characters = 'a-z0-9_\\-';
  const characters_allowed_for_title = '"a-z", "0-9", "_", "-"';

  public $title = 'ID';
  public $attributes = ['data-type' => 'id_text'];
  public $element_attributes = [
    'type'      => 'text',
    'name'      => 'id',
    'required'  => true,
    'maxlength' => 255
  ];

  function render_description() {
    if (!$this->description)
         $this->description = new text('Field value can contain only the next characters: %%_characters', ['characters' => static::characters_allowed_for_title]);
    return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_id($new_value)) {
      $field->error_set(
        'Field "%%_title" contains an error!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}