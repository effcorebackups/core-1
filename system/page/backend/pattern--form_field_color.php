<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_color extends form_field {

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