<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_radiobutton extends field_simple {

  public $title = 'Radiobutton';
  public $title_position = 'bottom';
  public $element_attributes_default = [
    'type' => 'radio',
    'name' => 'radio',
  ];

}}