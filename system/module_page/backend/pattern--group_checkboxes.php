<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_checkboxes extends group_radiobuttons {

  public $attributes = [
    'data-type' => 'checkboxes',
    'role'      => 'group'];
  public $field_class = '\\effcore\\field_checkbox';

  function values_get() {
    $result = [];
    foreach ($this->children_select() as $c_id => $c_child) {
      if ($c_child instanceof $this->field_class &&
          $c_child->checked_get() == true) {
        $result[$c_id] = $c_child->value_get();
      }
    }
    return $result;
  }

  function values_set($values) {
    foreach ($this->children_select() as $c_child) if ($c_child instanceof $this->field_class) $c_child->checked_set(false);
    foreach ($this->children_select() as $c_child) if ($c_child instanceof $this->field_class) {
      if (in_array($c_child->value_get(), $values)) {
        $c_child->checked_set(true);
      }
    }
  }

}}