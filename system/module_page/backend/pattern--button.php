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
  public $novalidate = false;

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

  function value_get() {
    return $this->attribute_select('value');
  }

  function disabled_get() {
    return $this->attribute_select('disabled') == 'disabled';
  }

  function disabled_set($is_disabled = true) {
    if ($is_disabled) $this->attribute_insert('disabled', 'disabled');
    else              $this->attribute_delete('disabled');
  }

}}