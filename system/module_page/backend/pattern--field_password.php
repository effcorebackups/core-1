<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_password extends field {

  public $title = 'Password';
  public $attributes = ['x-type' => 'password'];
  public $element_attributes_default = [
    'type'         => 'password',
    'name'         => 'password',
    'required'     => 'required',
    'autocomplete' => 'off',
    'minlength'    => 5,
    'maxlength'    => 255
  ];

}}