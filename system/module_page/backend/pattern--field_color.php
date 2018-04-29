<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_color extends field_simple {

  public $title = 'Color';
  public $element_attributes_default = [
    'type'     => 'color',
    'name'     => 'color',
    'required' => 'required',
    'value'    => '#ffffff'
  ];

}}