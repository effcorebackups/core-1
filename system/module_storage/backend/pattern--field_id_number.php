<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_id_number extends field_number {

  public $title = 'ID';
  public $attributes = ['data-type' => 'id_number'];
  public $element_attributes = [
    'type'     => 'number',
    'name'     => 'id',
    'required' => true,
    'min'      => 1,
    'max'      => self::input_max_number,
    'step'     => 1
  ];

}}