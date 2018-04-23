<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_palette extends group_radiobuttons {

  function build() {
    $this->attribute_insert('class', ['palette' => 'palette']);
    foreach (storage::get('files')->select('colors') as $c_module_id => $c_module_colors) {
      foreach ($c_module_colors as $c_row_id => $c_color) {
        $this->element_insert(null, [
          'value' => $c_color->id,
          'title' => translation::get('Color ID = %%_id (value = %%_value)', ['id' => $c_color->id, 'value' => $c_color->value]),
          'style' => ['background-color: '.$c_color->value]
        ], $c_color->id);
      }
    }
  }

}}