<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class control extends container {

  public $name_prefix = '';
  public $cform;
  protected $initial_value;

  function value_get()       {} # abstract method
  function value_set($value) {  # abstract method
    $this->value_set_initial($value);
  }

  function value_get_initial() {
    return $this->initial_value;
  }

  function value_set_initial($value, $reset = false) {
    if ($this->initial_value === null || $reset == true)
        $this->initial_value = $value;
  }

}}