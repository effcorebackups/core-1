<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_logic extends field_select {

  public $title = 'Logic';
  public $attributes = ['data-type' => 'logic'];
  public $element_attributes = [
    'name'     => 'logic',
    'required' => true
  ];
  public $values = [
    'not_selected' => '- select -',
    '0'            => 'no',
    '1'            => 'yes'
  ];

}}