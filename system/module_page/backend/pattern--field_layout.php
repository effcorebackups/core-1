<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_layout extends field_select {

  public $title = 'Layout';
  public $attributes = ['data-type' => 'layout'];
  public $element_attributes = [
    'name'     => 'layout',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->option_insert('- no -', 'not_selected');
      foreach (layout::select_all() as $c_layout) {
        $this->option_insert(
          $c_layout->title,
          $c_layout->id
        );
      }
      $this->is_builded = true;
    }
  }

}}