<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_range extends field_number {

  const input_min_range = 0;
  const input_max_range = 100;

  public $title = 'Range';
  public $attributes = ['data-type' => 'range'];
  public $element_attributes_default = [
    'type'     => 'range',
    'name'     => 'range',
    'required' => 'required',
    'min'      => self::input_min_range,
    'max'      => self::input_max_range,
    'step'     => 1,
    'value'    => 0
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_range;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_range;}

}}