<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_logic extends field_select {

  public $attributes = ['data-type' => 'logic'];
  public $element_attributes = [
    'name'     => 'logic',
    'required' => true
  ];
  public $values = [
    'not_selected' => '- no -',
    '0'            => 'No',
    '1'            => 'Yes'
  ];

}}