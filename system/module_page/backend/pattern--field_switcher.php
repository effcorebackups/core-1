<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_switcher extends field_checkbox {

  public $title = '';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'switcher'];
  public $element_attributes = [
    'type'      => 'checkbox',
    'name'      => 'checkbox',
    'data-type' => 'switcher',
    'value'     => 'on',
  ];

}}