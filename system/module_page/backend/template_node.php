<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class template_node extends template {

  public $pointers = [];

  function render() {
    foreach ($this->args as $c_name => $c_value) {
      $c_dpath = $this->pointers[$c_name];
      $c_pointers = core::dpath_pointers_get($this->data->children, $c_dpath, true);
      core::arrobj_value_insert($c_pointers, count($c_pointers) - 1, $c_value);
    }
    return $this->data->render();
  }

}}