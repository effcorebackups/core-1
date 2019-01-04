<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template_code extends template {

  public $handler = '';

  function render() {
    return call_user_func($this->handler, $this->args);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function get_copied_properties() {
    return ['handler' => 'handler'] + parent::get_copied_properties();
  }

}}