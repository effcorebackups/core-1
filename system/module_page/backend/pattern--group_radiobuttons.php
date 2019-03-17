<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_radiobuttons extends container implements group_mono {

  public $tag_name = 'x-group';
  public $attributes = ['data-type' => 'radiobuttons'];
# ─────────────────────────────────────────────────────────────────────
  public $field_class = '\\effcore\\field_radiobutton';
  public $field_tag_name = 'x-field';
  public $field_title_tag_name = 'label';
  public $field_title_position = 'bottom';
  public $element_attributes = [];
  public $values   = [];
  public $required = [];
  public $disabled = [];
  public $checked  = [];

  function __construct($values = null, $required = null, $disabled = null, $checked = null) {
    if ($values  ) $this->values   = $values;
    if ($required) $this->required = $required;
    if ($disabled) $this->disabled = $disabled;
    if ($checked ) $this->checked  = $checked;
    parent::__construct();
  }

  function build() {
    foreach ($this->values as $value => $title) {
      $this->field_insert($title, ['value' => $value]);
    }
  }

  function field_insert($title = null, $attributes = [], $new_id = null) {
    $field = new $this->field_class();
    $field->title          = $title;
    $field->tag_name       = $this->field_tag_name;
    $field->title_tag_name = $this->field_title_tag_name;
    $field->title_position = $this->field_title_position;
    $field->build();
    $element = $field->child_select('element');
    foreach ($attributes + $this->attributes_select('element_attributes') as $c_name => $c_value) {
      $element->attribute_insert($c_name, $c_value);
    }
    $value = $element->attribute_select('value');
    if (isset($this->required[$value])) $field->required_set();
    if (isset($this->checked [$value])) $field-> checked_set();
    if (isset($this->disabled[$value])) $field->disabled_set();
    return $this->child_insert($field, $new_id);
  }

  function name_first_get($trim = true) {
        $element_attributes_name = $this->attributes_select('element_attributes')['name'] ?? '';
        $element_attributes_name = $trim ? rtrim($element_attributes_name, '[]') : $element_attributes_name;
    if ($element_attributes_name) return
        $element_attributes_name;
  # search in first child (instance of field_class)
    else foreach ($this->children_select() as $c_item) {
      if ($c_item instanceof $this->field_class) {
        return $c_item->name_get($trim);
      }
    }
  }

  function value_get() {
    foreach ($this->children_select() as $c_item) {
      if ($c_item instanceof $this->field_class &&
          $c_item->checked_get() == true) {
        return $c_item->value_get();
      }
    }
    return '';
  }

  function value_set($value) {
    foreach ($this->children_select() as $c_item) if ($c_item instanceof $this->field_class) $c_item->checked_set(false);
    foreach ($this->children_select() as $c_item) if ($c_item instanceof $this->field_class) {
      if ($c_item->value_get() == $value) {
        $c_item->checked_set(true);
        return true;
      }
    }
  }

}}