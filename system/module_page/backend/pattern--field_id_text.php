<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_id_text extends field_text {

  const allowed_characters = 'a-z0-9_\\-';
  const allowed_characters_title = '"a-z", "0-9", "_", "-"';

  public $title = 'ID';
  public $attributes = ['data-type' => 'id_text'];
  public $element_attributes = [
    'type'      => 'text',
    'name'      => 'id',
    'required'  => true,
    'maxlength' => 255
  ];

  function render_description() {
    $this->description = new text('Field can contain only the next characters: %%_characters', ['characters' => self::allowed_characters_title]);
    return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_id($new_value)) {
      $field->error_set(
        'Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}