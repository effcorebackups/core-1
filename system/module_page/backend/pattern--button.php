<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class button extends markup {

  public $tag_name = 'button';
  public $attributes = ['type' => 'submit', 'name' => 'button'];
# ─────────────────────────────────────────────────────────────────────
  public $title = 'button';

  function __construct($title = null, $attributes = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->child_select('label')) {
      $label = new text($this->title);
      $this->child_insert($label, 'label');
    }
  }

}}