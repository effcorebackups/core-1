<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_weight extends field_number {

  const input_min_weight = -1000;
  const input_max_weight = +1000;

  public $title = 'Weight';
  public $attributes = ['data-type' => 'weight'];
  public $element_attributes = [
    'type'     => 'number',
    'name'     => 'weight',
    'required' => true,
    'min'      => self::input_min_weight,
    'max'      => self::input_max_weight,
    'step'     => 1,
    'value'    => 0
  ];

}}