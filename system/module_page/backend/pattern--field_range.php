<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_range extends field_number {

  const input_min_range = 0;
  const input_max_range = 100;

  public $title = 'Range';
  public $attributes = ['data-type' => 'range'];
  public $element_attributes = [
    'type'     => 'range',
    'name'     => 'range',
    'required' => true,
    'min'      => self::input_min_range,
    'max'      => self::input_max_range,
    'step'     => 1,
    'value'    => 0
  ];

}}