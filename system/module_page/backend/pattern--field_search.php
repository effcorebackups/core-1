<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_search extends field_simple {

  public $title = 'Search';
  public $element_attributes_default = [
    'type'      => 'search',
    'name'      => 'search',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 255
  ];

}}