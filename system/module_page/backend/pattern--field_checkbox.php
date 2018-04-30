<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox extends field_simple {

  public $title = 'Checkbox';
  public $title_position = 'bottom';
  public $element_attributes_default = [
    'type' => 'checkbox',
    'name' => 'checkbox'
  ];

}}