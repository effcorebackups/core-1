<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class table extends markup {

  public $tag_name = 'table';

  function __construct($attributes = [], $tbody = [], $thead = [], $weight = 0) {
    parent::__construct(null, $attributes, [], $weight);
    if ($thead) $this->child_insert(new table_head([], $thead), 'head');
    if ($tbody) $this->child_insert(new table_body([], $tbody), 'body');
  }

}}