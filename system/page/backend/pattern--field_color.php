<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_color extends field {

  public $title = 'Color';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'     => 'color',
      'name'     => 'color',
      'required' => 'required',
      'value'    => '#ffffff'
    ]), 'element');
    parent::build();
  }

}}