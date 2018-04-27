<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_checkboxes extends container {

  public $tag_name = 'x-group';
  public $field_tag_name = 'x-field';
  public $field_title_tag_name = 'label';
  public $field_title_position = 'bottom';
  public $element_tag_name = 'checkbox';
  public $element_attributes = [];
  public $values = [];
  public $required = [];
  public $disabled = [];
  public $checked = [];

  function __construct($values = null, $required = null, $disabled = null, $checked = null) {
    if ($values)   $this->values   = $values;
    if ($required) $this->required = $required;
    if ($disabled) $this->disabled = $disabled;
    if ($checked)  $this->checked  = $checked;
    parent::__construct();
  }

  function build() {
    $this->attribute_insert('class', factory::array_kmap(['boxes', $this->element_tag_name]));
    foreach ($this->values as $value => $title) {
      $this->field_insert($title, ['value' => $value]);
    }
  }

  function field_insert($title = null, $attr = [], $new_id = null) {
    $element = new markup_simple('input', ['type' => $this->element_tag_name] + $attr + $this->attribute_select_all('element_attributes'));
    $value = $element->attribute_select('value');
    if (isset($this->required[$value])) $element->attribute_insert('required', 'required');
    if (isset($this->checked[$value]))  $element->attribute_insert('checked',   'checked');
    if (isset($this->disabled[$value])) $element->attribute_insert('disabled', 'disabled');
    $field = new field($this->field_tag_name, $title);
    $field->title_tag_name = $this->field_title_tag_name;
    $field->title_position = $this->field_title_position;
    $field->child_insert($element, 'element');
    return $this->child_insert($field, $new_id);
  }

  function default_set($value) {
    foreach ($this->child_select_all() as $c_field) {
      $c_element = $c_field->child_select('element');
      if ($c_element->attribute_select('value') == $value) {
        return $c_element->attribute_insert('checked', 'checked');
      }
    }
  }

  function default_get() {
    foreach ($this->child_select_all() as $c_field) {
      $c_element = $c_field->child_select('element');
      if ($c_element->attribute_select('checked') == 'checked') {
        return $c_element->attribute_select('value');
      }
    }
  }

}}