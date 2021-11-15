<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_password extends field_text {

  public $title = 'Password';
  public $attributes = ['data-type' => 'password'];
  public $element_attributes = [
    'type'      => 'password',
    'name'      => 'password',
    'required'  => true,
    'minlength' => 5,
    'maxlength' => 255
  ];

  function value_get($return_hash = true) {
    $element = $this->child_select('element');
    $value = $element->attribute_select('value');
    if ($value !== '' && $return_hash !== false) return user::password_hash($value);
    if ($value !== '' && $return_hash === false) return                     $value;
    if ($value === ''                          ) return                     $value;
  }

}}