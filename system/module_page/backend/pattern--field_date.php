<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_simple {

  public $title = 'Date';
  public $element_attributes_default = [
    'type'     => 'date',
    'name'     => 'date',
    'required' => 'required',
    'min'      => '0001-01-01',
    'max'      => '9999-31-12'
  ];

  function build() {
    $this->attribute_insert('value', factory::date_get(), 'element_attributes_default');
    parent::build();
  }

}}