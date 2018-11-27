<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_timezone extends field_select {

  public $attributes = ['data-type' => 'timezone'];

  function build() {
    parent::build();
    $this->option_insert('- select -', 'not_selected');
    foreach (\DateTimeZone::listIdentifiers() as $c_title) {
      $this->option_insert($c_title, $c_title);
    }
  }

}}