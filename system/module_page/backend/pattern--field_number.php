<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field_simple {

  public $title = 'Number';
  public $element_attributes_default = [
    'type'     => 'number',
    'name'     => 'number',
    'required' => 'required',
    'min'      => form::input_min_number,
    'max'      => form::input_max_number,
    'step'     => 1,
    'value'    => 0
  ];

}}