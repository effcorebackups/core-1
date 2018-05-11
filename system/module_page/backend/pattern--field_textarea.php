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

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $dpath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($field, $form, $dpath, $element, $new_value) &&
                static::validate_minlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_value    ($field, $form, $dpath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $dpath, $element, $new_value);
      $element->child_select('content')->text_update($new_value);
      return $result;
    }
  }

}}