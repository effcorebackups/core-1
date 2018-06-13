<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_textarea extends field_text {

  public $title = 'Textarea';
  public $attributes = ['x-type' => 'textarea'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'textarea';
  public $element_attributes_default = [
    'name'      => 'textarea',
    'required'  => 'required',
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

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::cur_index_get($name);
      $new_value = static::new_value_get($name, $cur_index);
      $result = static::validate_required ($field, $form, $npath, $element, $new_value) &&
                static::validate_minlength($field, $form, $npath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $npath, $element, $new_value) &&
                static::validate_value    ($field, $form, $npath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $npath, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

}}