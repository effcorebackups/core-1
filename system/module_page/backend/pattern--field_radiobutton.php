<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_radiobutton extends field {

  public $title = 'Radiobutton';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'radiobutton'];
  public $element_attributes_default = [
    'type' => 'radio',
    'name' => 'radio'
  ];

  function value_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('value');
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      $new_values = static::request_values_get($name, $form->source_get());
      $result = static::validate_required($field, $form, $element, $new_values);
      $field->element_checked_set(in_array($field->value_get(), $new_values));
      return $result;
    }
  }

  static function validate_required($field, $form, $element, &$new_values) {
    if ($element->attribute_select('required') && !core::in_array_string_compare($element->attribute_select('value'), $new_values)) {
      $field->error_add(
        translation::get('Field "%%_title" must be checked!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}