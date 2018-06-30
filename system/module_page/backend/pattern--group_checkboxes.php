<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_checkboxes extends group_radiobuttons {

  public $attributes = ['data-type' => 'checkboxes'];
  public $field_class = '\\effcore\\field_checkbox';

  function values_get() {
    $return = [];
    foreach ($this->children_select() as $c_id => $c_field) {
      if ($c_field->element_checked_get() == true) {
        $return[$c_id] = $c_field->value_get();
      }
    }
    return $return;
  }

  function values_set($values) {
    foreach ($this->children_select() as $c_field) $c_field->element_checked_set(false);
    foreach ($this->children_select() as $c_field) {
      if (in_array($c_field->value_get(), $values)) {
        $c_field->element_checked_set(true);
      }
    }
  }

}}