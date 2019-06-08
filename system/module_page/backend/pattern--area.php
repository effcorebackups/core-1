<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class area extends markup {

  public $tag_name = 'x-area';
  public $id;
  public $title;
  public $type; # null | table | row | column
  public $is_managed = false;

  function render() {
    if ($this->type) $this->attribute_insert('data-type', $this->type);
    if ($this->id)   $this->attribute_insert('data-id',   $this->id);
    if ($this->is_managed &&
        $this->id) $this->child_insert(new text_simple($this->id), 'id');
    return parent::render();
  }

}}