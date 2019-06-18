<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_text_direction extends field_select {

  public $title = 'Text direction';
  public $attributes = ['data-type' => 'text_direction'];
  public $element_attributes = [
    'name'     => 'text_direction',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->option_insert('- no -', 'not_selected');
      $this->option_insert('left to right', 'ltr');
      $this->option_insert('right to left', 'rtl');
      $this->is_builded = true;
    }
  }

}}