<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_time extends field_simple {

  public $title = 'Time';
  public $element_attributes_default = [
    'type'     => 'time',
    'name'     => 'time',
    'required' => 'required',
    'min'      => '00:00:00',
    'max'      => '23:59:59',
    'step'     => 60
  ];

  function build() {
    $this->attribute_insert('value', factory::time_get(), 'element_attributes_default');
    parent::build();
  }

}}