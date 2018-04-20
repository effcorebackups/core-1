<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url extends field {

  public $title = 'URL';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'      => 'url',
      'name'      => 'url',
      'required'  => 'required',
      'minlength' => 5,
      'maxlength' => 255
    ]), 'element');
    parent::build();
  }

}}