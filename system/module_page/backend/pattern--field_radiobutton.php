<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_radiobutton extends field {

  public $title = 'Radiobutton';
  public $title_position = 'bottom';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type' => 'radio',
      'name' => 'radio',
    ]), 'element');
    parent::build();
  }

}}