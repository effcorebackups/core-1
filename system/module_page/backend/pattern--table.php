<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class table extends markup {

  public $tag_name = 'table';

  function __construct($attributes = [], $tbody = [], $thead = [], $weight = 0) {
    parent::__construct(null, $attributes, [], $weight);
    if ($thead instanceof table_head !== false) $this->child_insert(                   $thead,  'head');
    if ($thead instanceof table_head === false) $this->child_insert(new table_head([], $thead), 'head');
    if ($tbody instanceof table_body !== false) $this->child_insert(                   $tbody,  'body');
    if ($tbody instanceof table_body === false) $this->child_insert(new table_body([], $tbody), 'body');
  }

}}