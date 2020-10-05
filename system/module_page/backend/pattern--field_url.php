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
    'maxlength' => 255
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    $url = new url($new_value);
    if ($url->has_error === true || core::validate_url(url::utf8_encode($new_value)) === false) {
      $field->error_set(
        'Field "%%_title" contains an incorrect URL!', ['title' => (new text($field->title))->render() ]
      );
    } else {
      return true;
    }
  }

}}