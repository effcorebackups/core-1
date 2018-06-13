<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_phone extends field_text {

  public $title = 'Phone';
  public $description = 'Use international format of mobile phone numbers.';
  public $attributes = ['x-type' => 'phone'];
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

  static function validate_value($field, $form, $npath, $element, &$new_value) {
    if (!core::validate_phone($new_value)) {
      $form->error_add($npath.'/element',
        translation::get('Field "%%_title" contains an incorrect phone!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}