<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class control extends container {

  public $cform;
  protected $initial_value;

  function form_current_set($form) {
    $this->cform = $form;
  }

  function value_get_initial() {
    return $this->initial_value;
  }

  function value_set_initial($value, $reset = false) {
    if ($this->initial_value === null || $reset == true)
        $this->initial_value = $value;
  }

}}