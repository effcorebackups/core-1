<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_radiobutton extends field {

  public $title = '';
  public $title_position = 'bottom';
  public $attributes = ['data-type' => 'radiobutton'];
  public $element_attributes_default = [
    'type' => 'radio',
    'name' => 'radio'
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      $new_values = static::request_values_get($name, $form->source_get());
      $result = static::validate_required($field, $form, $element, $new_values);
      $field->checked_set(core::in_array_string_compare($field->value_get(), $new_values));
      return $result;
    }
  }

  static function validate_required($field, $form, $element, &$new_values) {
    if ($field->required_get() && !core::in_array_string_compare($field->value_get(), $new_values)) {
      $field->error_set(
        'Field "%%_title" must be checked!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}