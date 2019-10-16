<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_textarea extends field_text {

  public $title = 'Text area';
  public $attributes = ['data-type' => 'textarea'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'textarea';
  public $element_attributes = [
    'name'      => 'textarea',
    'required'  => true,
    'rows'      => 5,
    'minlength' => 5,
    'maxlength' => 255
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $value = $this->attribute_select('value', 'element_attributes');
               $this->attribute_delete('value', 'element_attributes');
      $element = $this->child_select('element');
      $element->child_insert(new text_simple($value ?: ''), 'content');
      $this->is_builded = true;
    }
  }

  function value_get() {
    $element = $this->child_select('element');
    return  $element->child_select('content')->text_select();
  }

  function value_set($value) {
    $this->value_set_initial($value);
    $element = $this->child_select('element');
    return  $element->child_select('content')->text_update($value);
  }

}}