<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page_part_preset_link {

  public $id;
  public $preset_origin = 'nosql'; # nosql | dynamic
  public $weight = 0;

  function __construct($id = null, $preset_origin = 'nosql', $weight = 0) {
    if ($id)            $this->id            = $id;
    if ($preset_origin) $this->preset_origin = $preset_origin;
    if ($weight)        $this->weight        = $weight;
  }

  function page_part_preset_get() {
    return page_part_preset::select($this->id);
  }

  function page_part_make() {
    if ($this->preset_origin == 'dynamic')
      event::start('on_page_parts_dynamic_build', null, [$this->id]);
    $preset = $this->page_part_preset_get($this->id);
    if (isset($preset)) {
      return $preset->page_part_make();
    }
  }

}}