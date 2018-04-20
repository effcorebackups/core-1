<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field {

  public $title = 'Date';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'     => 'date',
      'name'     => 'date',
      'required' => 'required',
      'value'    => factory::date_get(),
      'min'      => '0001-01-01',
      'max'      => '9999-31-12'
    ]), 'element');
    parent::build();
  }

}}