<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox_color extends field_checkbox {

  public $title = 'Color';
  public $attributes = [
    'data-type' => 'checkbox-color'
  ];

  function color_set($color_id) {
    $colors = color::get_all();
    $element = $this->child_select('element');
    if (isset($colors[$color_id])) {
      $element->attribute_insert('style', 'background: '.$colors[$color_id]->value          );
      $element->attribute_insert('data-value',           $colors[$color_id]->value          );}
      $element->attribute_insert('aria-invalid',   isset($colors[$color_id]) ? null : 'true');
  }

}}