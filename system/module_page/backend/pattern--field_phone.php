<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_phone extends field {

  public $title = 'Phone';
  public $attributes = ['x-type' => 'phone'];
  public $element_attributes_default = [
    'type'      => 'tel',
    'name'      => 'phone',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 15
  ];

}}