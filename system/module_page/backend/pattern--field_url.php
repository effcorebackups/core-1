<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url extends field_text {

  public $title = 'URL';
  public $attributes = ['x-type' => 'url'];
  public $element_attributes_default = [
    'type'      => 'url',
    'name'      => 'url',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 255
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $npath, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_url($new_value)) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" contains an incorrect URL!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}