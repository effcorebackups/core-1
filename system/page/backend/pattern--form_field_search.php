<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_search extends form_field {

  public $title = 'Search';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'      => 'search',
      'name'      => 'search',
      'required'  => 'required',
      'minlength' => 5,
      'maxlength' => 255
    ]), 'element');
    parent::build();
  }

}}