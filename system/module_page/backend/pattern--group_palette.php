<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_palette extends group_radiobuttons {

  public $attributes = ['data-type' => 'palette'];

  function build() {
  # parent::build() not required
    foreach (storage::get('files')->select('colors') as $c_colors) {
      foreach ($c_colors as $c_row_id => $c_color) {
        $this->field_insert(null, [
          'value' => $c_color->id,
          'title' => translation::get('Color ID = %%_id (value = %%_value)', ['id' => $c_color->id, 'value' => $c_color->value]),
          'style' => ['background: '.$c_color->value]
        ], $c_color->id);
      }
    }
  }

}}