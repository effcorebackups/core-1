<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_textarea extends field_simple {

  public $title = 'Textarea';
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'textarea';
  public $element_attributes_default = [
    'name'      => 'textarea',
    'required'  => 'required',
    'rows'      => 5,
    'minlength' => 5,
    'maxlength' => 255,
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

}}