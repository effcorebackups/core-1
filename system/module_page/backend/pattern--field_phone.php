<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_phone extends field_simple {

  public $title = 'Phone';
  public $element_attributes_default = [
    'type'      => 'tel',
    'name'      => 'phone',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 15,
  ];

}}