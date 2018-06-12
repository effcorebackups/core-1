<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_checkboxes extends group_radiobuttons {

  public $attributes = ['x-type' => 'checkboxes'];
  public $field_class = '\\effcore\\field_checkbox';

  function values_get() {
    $return = [];
    foreach ($this->children_select() as $c_id => $c_field)
                                 $return[$c_id] = $c_field->value_get();
    return $return;
  }

  function values_set($values) {
    foreach ($this->children_select() as $c_field) {
      $c_element = $c_field->child_select('element');
      if (in_array($c_element->attribute_select('value'), $values)) {
        $c_element->attribute_insert('checked', 'checked');
      }
    }
  }

}}