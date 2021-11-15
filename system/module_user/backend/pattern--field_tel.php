<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_tel extends field_text {

  public $title = 'Telephone number';
  public $description = 'Field value should be represented in the international format of telephone numbers.';
  public $attributes = ['data-type' => 'tel'];
  public $element_attributes = [
    'type'      => 'tel',
    'name'      => 'tel',
    'required'  => true,
    'minlength' => 5,
    'maxlength' => 15
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_tel($new_value)) {
      $field->error_set(
        'Field "%%_title" contains an incorrect telephone number!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}