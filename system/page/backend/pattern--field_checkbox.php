<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox extends field {

  public $title = 'Checkbox';
  public $title_position = 'bottom';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type' => 'checkbox',
      'name' => 'checkbox',
    ]), 'element');
    parent::build();
  }

}}