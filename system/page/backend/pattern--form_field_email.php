<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_email extends form_field {

  public $title = 'EMail';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'      => 'email',
      'name'      => 'email',
      'required'  => 'required',
      'minlength' => 5,
      'maxlength' => 64
    ]), 'element');
    parent::build();
  }

}}