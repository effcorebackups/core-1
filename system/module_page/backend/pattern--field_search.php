<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_search extends field_text {

  public $title = 'Search';
  public $attributes = ['x-type' => 'search'];
  public $element_attributes_default = [
    'type'      => 'search',
    'name'      => 'search',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 255
  ];

}}