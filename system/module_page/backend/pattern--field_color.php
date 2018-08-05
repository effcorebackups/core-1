<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_color extends field_text {

  public $title = 'Color';
  public $description = 'The color should be in the format "#abcdef", where "ab" is the value of the red component, "cd" - green and "ef" - blue.';
  public $attributes = ['data-type' => 'color'];
  public $element_attributes_default = [
    'type'      => 'color',
    'name'      => 'color',
    'required'  => 'required',
    'value'     => '#ffffff'
  ];

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
      $new_value = static::request_value_get($name, $cur_index, $form->source_get());
      $new_value = strtolower($new_value);
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_value    ($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (!core::validate_hex_color($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('The color should be specified in a special format.')
      );
    } else {
      return true;
    }
  }

}}