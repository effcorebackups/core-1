<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class button extends markup {

  public $tag_name = 'button';
  public $attributes = [
    'type' => 'submit',
    'name' => 'button'];
# ─────────────────────────────────────────────────────────────────────
  public $title = 'button';
  public $novalidate = false;

  function __construct($title = null, $attributes = [], $weight = 0) {
    if ($title !== null) $this->title = $title;
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $label = new text($this->title);
      $this->child_insert($label, 'label');
      $this->is_builded = true;
    }
  }

  function value_get() {
    return $this->attribute_select('value');
  }

  function value_set($value) {
    return $this->attribute_insert('value', $value);
  }

  function disabled_get() {
    return $this->attribute_select('disabled') === 'disabled' ||
           $this->attribute_select('disabled') === true;
  }

  function disabled_set($is_disabled = true) {
    if ($is_disabled) $this->attribute_insert('disabled', true);
    else              $this->attribute_delete('disabled');
  }

  function is_clicked($number = 0, $source = '_POST') {
    $request_value = field::request_value_get('button', $number, $source);
    if ($this->disabled_get() == false &&
        $request_value                 &&
        $request_value        == $this->value_get()) {
      return true;
    }
  }

}}