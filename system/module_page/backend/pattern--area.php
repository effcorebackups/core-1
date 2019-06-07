<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class area extends markup {

  public $id;
  public $title;
  public $tag_name = 'x-area';
  public $type; # null | table | row | column

  function render() {
    if ($this->type) $this->attribute_insert('data-type', $this->type);
                     $this->attribute_insert('data-id',   $this->id);
    if ($this->id) $this->child_insert(new text_simple($this->id), 'id');
    return parent::render();
  }

}}