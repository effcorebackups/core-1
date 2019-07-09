<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_id_text extends field_text {

  public $title = 'ID';
  public $attributes = ['data-type' => 'id_text'];
  public $element_attributes = [
    'type'      => 'text',
    'name'      => 'text',
    'pattern'   => '^[a-z0-9_\-]+$',
    'required'  => true,
    'maxlength' => 255
  ];

}}