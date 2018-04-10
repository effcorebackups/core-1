<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_email extends form_field {

  public $element_attributes = [];

  function build() {
    $this->child_insert(new markup_simple('input', $this->element_attributes + [
      'type'      => 'email',
      'name'      => 'email',
      'required'  => 'required',
      'minlength' => 5,
      'maxlength' => 64
    ]), 'element');
  }

}}