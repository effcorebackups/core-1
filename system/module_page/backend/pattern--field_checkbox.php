<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox extends field_radiobutton {

  public $title = '';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'checkbox'];
  public $element_attributes = [
    'type'  => 'checkbox',
    'name'  => 'checkbox',
    'value' => 'on'
  ];

  function render() {
    $element = $this->child_select('element');
    $element->attribute_insert('data-is-checked', $this->checked_get() ? 'yes' : 'no');
    return parent::render();
  }

}}