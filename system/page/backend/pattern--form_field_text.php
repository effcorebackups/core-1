<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_text extends form_field {

  public $title = 'Text';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'      => 'text',
      'name'      => 'text',
      'required'  => 'required',
      'maxlength' => 255
    ]), 'element');
    parent::build();
  }

}}