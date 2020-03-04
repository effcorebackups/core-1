<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class control extends container {

  public $cform;

  function form_current_set($form) {
    $this->cform = $form;
  }

}}