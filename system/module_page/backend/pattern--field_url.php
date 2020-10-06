<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url extends field_text {

  public $title = 'URL';
  public $attributes = ['data-type' => 'url'];
  public $element_attributes = [
    'type'      => 'url',
    'name'      => 'url',
    'required'  => true,
    'minlength' => 1,
    'maxlength' => 2047
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if ((strlen($new_value) && (new url($new_value))->has_error === true)) {
      $field->error_set(
        'Field "%%_title" contains an incorrect URL!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}