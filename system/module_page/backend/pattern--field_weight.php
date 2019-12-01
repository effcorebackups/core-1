<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_weight extends field_number {

  public $title = 'Weight';
  public $attributes = ['data-type' => 'weight'];
  public $element_attributes = [
    'type'     => 'number',
    'name'     => 'weight',
    'required' => true,
    'min'      => -1000,
    'max'      => +1000,
    'step'     => 1,
    'value'    => 0
  ];

}}