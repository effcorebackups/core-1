<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class part_preset_link {

  public $id;
  public $weight = 0;

  function __construct($id = null, $weight = 0) {
    if ($id    ) $this->id     = $id;
    if ($weight) $this->weight = $weight;
  }

  function part_make() {
    $preset = core::deep_clone(part_preset::select($this->id));
    if ($preset) {
      foreach ($this as $c_key => $c_value)
        $preset->{$c_key} =
        $this  ->{$c_key};
      return $preset->part_make();
    }
  }

}}