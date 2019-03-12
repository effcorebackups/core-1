<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_textarea extends field_text {

  public $title = 'Textarea';
  public $attributes = ['data-type' => 'textarea'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'textarea';
  public $element_attributes_default = [
    'name'      => 'textarea',
    'required'  => true,
    'rows'      => 5,
    'minlength' => 5,
    'maxlength' => 255
  ];

  function build() {
    $value_def = $this->attribute_select('value', 'element_attributes_default') ?: '';
    $value     = $this->attribute_select('value', 'element_attributes')         ?: '';
                 $this->attribute_delete('value', 'element_attributes_default');
                 $this->attribute_delete('value', 'element_attributes');
    parent::build();
    $element = $this->child_select('element');
    $element->child_insert(new text_simple($value ?: $value_def), 'content');
  }

  function value_get() {
    $element = $this->child_select('element');
    return $element->child_select('content')->text_select();
  }

  function value_set($value) {
    $element = $this->child_select('element');
    return $element->child_select('content')->text_update($value);
  }

}}