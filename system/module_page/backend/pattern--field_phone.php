<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_phone extends field_text {

  public $title = 'Phone';
  public $description = 'Use international format of mobile phone numbers.';
  public $attributes = ['data-type' => 'phone'];
  public $element_attributes_default = [
    'type'      => 'tel',
    'name'      => 'phone',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 15
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (!core::validate_phone($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect phone!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}