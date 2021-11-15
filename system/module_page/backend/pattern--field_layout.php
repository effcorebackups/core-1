<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_layout extends field_select {

  public $title = 'Layout';
  public $title__not_selected = '- select -';
  public $attributes = ['data-type' => 'layout'];
  public $element_attributes = [
    'name'     => 'layout',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->option_insert($this->title__not_selected, 'not_selected');
      $options = [];
      foreach (layout::select_all() as $c_layout)
        $options[$c_layout->id] = (new text_multiline([
          'title' => $c_layout->title, 'id' => '('.$c_layout->id.')'], [], ' '
        ))->render();
      core::array_sort_text($options);
      foreach ($options as $c_id => $c_title)
        $this->option_insert($c_title, $c_id);
      $this->is_builded = true;
    }
  }

}}