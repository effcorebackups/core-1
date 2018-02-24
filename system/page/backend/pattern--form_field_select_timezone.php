<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class form_field_select_timezone extends \effectivecore\form_field_select {

  function build() {
    parent::build();
    $this->option_insert('- select -', 'not_selected');
    foreach (\DateTimeZone::listIdentifiers() as $c_id => $c_title) {
      $this->option_insert($c_title, $c_id);
    }
  }

}}