<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_hidden extends markup_simple {

  public $tag_name = 'input';
  public $attributes = [
    'type' => 'hidden',
  ];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, $attributes, $weight);
  }

  # ─────────────────────────────────────────────────────────────────────
  # element properties
  # ─────────────────────────────────────────────────────────────────────

  function name_get($trim = true) {
    return $trim ? rtrim($this->attribute_select('name'), '[]') :
                         $this->attribute_select('name');
  }

  function name_set($name) {
    $this->attribute_insert('name', $name);
  }

  function type_get($full = true) {
    if ($full) return 'input:'.$this->attribute_select('type');
    else       return 'input';
  }

  function value_get() {
    return $this->attribute_select('value');
  }

  function value_set($value) {
    return $this->attribute_insert('value', $value);
  }

}}