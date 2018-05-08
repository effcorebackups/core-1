<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_range extends field_number {

  public $title = 'Range';
  public $attributes = ['x-type' => 'range'];
  public $element_attributes_default = [
    'type'     => 'range',
    'name'     => 'range',
    'required' => 'required',
    'min'      => 0,
    'max'      => 100,
    'step'     => 1,
    'value'    => 0
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function get_min_value($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : 0;}
  static function get_max_value($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : 100;}

}}