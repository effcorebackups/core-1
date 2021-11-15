<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_hidden extends markup_simple {

  public $tag_name = 'input';
  public $attributes = [
    'type' => 'hidden',
  ];

  function __construct($name = null, $value = null, $attributes = [], $weight = 0) {
    if ($name          ) $this-> name_set($name );
    if ($value !== null) $this->value_set($value);
    parent::__construct(null, $attributes, $weight);
  }

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

  function value_request_get($number = 0, $source = '_POST') {
    return request::value_get($this->name_get(), $number, $source);
  }

  function value_get() {
    return $this->attribute_select('value');
  }

  function value_set($value) {
    return $this->attribute_insert('value', $value);
  }

}}