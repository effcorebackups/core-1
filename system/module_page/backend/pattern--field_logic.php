<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_logic extends field_select {

  public $attributes = ['data-type' => 'logic'];
  public $element_attributes = [
    'name'     => 'logic',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->option_insert('- select -', 'not_selected');
      $this->option_insert('No',  '0');
      $this->option_insert('Yes', '1');
      $this->is_builded = true;
    }
  }

}}