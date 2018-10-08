<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_nick extends field_text {

  public $title = 'Nick';
  public $attributes = ['data-type' => 'nick'];
  public $element_attributes_default = [
    'name'      => 'nick',
    'required'  => 'required',
    'pattern'   => '%^[a-zA-Z0-9-_]{4,32}$%',
    'minlength' => 4,
    'maxlength' => 32
  ];

}}