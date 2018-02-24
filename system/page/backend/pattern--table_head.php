<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class table_head extends \effectivecore\markup {

  public $tag_name = 'thead';

  function __construct($attributes = [], $children = [], $weight = 0) {
    parent::__construct($this->tag_name, $attributes, $children, $weight);
  }

  function child_insert($child, $id = null) {
    if ($child instanceof table_body_row) return parent::child_insert($child, $id);
    if ($child instanceof instance)       return parent::child_insert(new table_head_row([], $child->get_values()), $id);
    if (is_array($child))                 return parent::child_insert(new table_head_row([], $child), $id);
  }

}}