<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field {

  public $title = 'Number';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'     => 'number',
      'name'     => 'number',
      'required' => 'required',
      'value'    => 0,
      'step'     => 1,
      'min'      => -10000000000,
      'max'      => +10000000000
    ]), 'element');
    parent::build();
  }

}}