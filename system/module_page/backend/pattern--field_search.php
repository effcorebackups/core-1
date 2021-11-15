<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_search extends field_text {

  public $title = 'Search';
  public $attributes = ['data-type' => 'search'];
  public $element_attributes = [
    'type'      => 'search',
    'name'      => 'search',
    'required'  => true,
    'minlength' => 5,
    'maxlength' => 255
  ];

}}