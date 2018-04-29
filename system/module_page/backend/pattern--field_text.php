<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_text extends field_simple {

  public $title = 'Text';
  public $element_attributes_default = [
    'type'      => 'text',
    'name'      => 'text',
    'required'  => 'required',
    'maxlength' => 255
  ];

}}