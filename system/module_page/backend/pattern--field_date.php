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
    'min'      => form::input_min_date,
    'max'      => form::input_max_date
  ];

  function build() {
    $this->attribute_insert('value', factory::date_get(), 'element_attributes_default');
    parent::build();
  }

}}