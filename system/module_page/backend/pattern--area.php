<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class area extends markup {

  public $tag_name = 'x-area';
  public $type; # null | table | row | col

  function render() {
    if ($this->type) $this->attribute_insert('data-type', $this->type);
    return parent::render();
  }

}}