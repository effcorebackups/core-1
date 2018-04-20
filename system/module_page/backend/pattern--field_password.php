<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_password extends field {

  public $title = 'Password';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'         => 'password',
      'name'         => 'password',
      'required'     => 'required',
      'autocomplete' => 'off',
      'minlength'    => 5,
      'maxlength'    => 255
    ]), 'element');
    parent::build();
  }

}}