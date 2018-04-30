<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_range extends field_simple {

  public $title = 'Range';
  public $element_attributes_default = [
    'type'     => 'range',
    'name'     => 'range',
    'required' => 'required',
    'min'      => form_input_min_number,
    'max'      => form_input_max_number,
    'step'     => 1,
    'value'    => 0
  ];

}}