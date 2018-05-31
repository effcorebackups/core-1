<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_phone extends field_text {

  public $title = 'Phone';
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
    if (strlen($new_value) &&
       !preg_match('%^\\+[0-9]{1,14}$%', $new_value, $matches)) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" contains an incorrect phone!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}