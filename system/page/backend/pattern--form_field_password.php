<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_password extends form_field {

  public $title = 'Password';

  function build() {
    $this->child_insert(new markup_simple('input', $this->element_attributes + [
      'type'         => 'password',
      'name'         => 'password',
      'required'     => 'required',
      'autocomplete' => 'off',
      'minlength'    => 5,
      'maxlength'    => 255
    ]), 'element');
  }

}}