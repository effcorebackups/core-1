<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_time extends form_field {

  public $title = 'Time';

  function build() {
    $this->child_insert(new markup_simple('input', [
      'type'     => 'time',
      'name'     => 'time',
      'required' => 'required',
      'value'    => factory::time_get(),
      'min'      => '00:00:00',
      'max'      => '23:59:59',
      'step'     => 60
    ]), 'element');
    parent::build();
  }

}}