<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_switcher extends field_checkbox {

  public $title = 'Checkbox';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'switcher'];
  public $element_attributes_default = [
    'type' => 'checkbox',
    'name' => 'checkbox'
  ];

  function build() {
    parent::build();
    $element = $this->child_select('element');
    if ($element->tag_name == 'input') {
      $this->child_update('element', new markup('x-wrapper', [], $element));
    }
  }

}}