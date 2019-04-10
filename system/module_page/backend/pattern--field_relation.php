<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_relation extends field_select {

  public $attributes = ['data-type' => 'relation'];
  public $element_attributes = [
    'name'     => 'relation',
    'required' => true
  ];

  function build() {
    parent::build();
    $this->option_insert('- select -', 'not_selected');
  }

}}