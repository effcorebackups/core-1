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
    foreach ($this->children_select() as $c_id => $c_field) {
      if ($c_field->value_get()) {
        $return[$c_id] = $c_field->value_get();
      }
    }
    return $return;
  }

  function values_set($values) {
    foreach ($this->children_select() as $c_field) $c_field->value_set('');
    foreach ($this->children_select() as $c_field) {
      $value_default = $c_field->value_get(true);
      if (in_array($value_default, $values)) {
        $c_field->value_set($value_default);
      }
    }
  }

}}