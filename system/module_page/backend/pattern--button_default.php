<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class button_default extends button {

  public $title = null;
  public $attributes = [
    'tabindex'  => -1,
    'data-type' => 'default',
    'type'      => 'submit',
    'name'      => 'button'];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded)
         $this->is_builded = true;
  }

}}