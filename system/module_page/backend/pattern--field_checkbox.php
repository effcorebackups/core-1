<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox extends field_radiobutton {

  public $title = 'Checkbox';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'checkbox'];
  public $element_attributes_default = [
    'type' => 'checkbox',
    'name' => 'checkbox'
  ];

}}