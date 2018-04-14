<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_phone extends form_field {

  public $title = 'Phone';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'      => 'tel',
      'name'      => 'phone',
      'required'  => 'required',
      'minlength' => 5,
      'maxlength' => 15,
    ]), 'element');
    parent::build();
  }

}}