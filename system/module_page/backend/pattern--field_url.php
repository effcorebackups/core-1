<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
    'maxlength' => 255
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if ( (strlen($new_value) &&  core::sanitize_url(         $new_value) != $new_value) ||
         (strlen($new_value) && !core::validate_url((new url($new_value))->full_get())) ) {
      $field->error_set(
        'Field "%%_title" contains an incorrect URL!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}