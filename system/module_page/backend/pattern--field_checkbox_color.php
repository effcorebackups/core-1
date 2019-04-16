<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox_color extends field_checkbox {

  public $attributes = ['data-type' => 'checkbox-color'];

  function color_set($color_id) {
    $colors = color::get_all();
    $element = $this->child_select('element');
    $element->attribute_insert('style', 'background: '.$colors[$color_id]->value);
  }

}}