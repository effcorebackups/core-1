<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = static::request_value_get($name, static::cur_number_get($name), $form->source_get());
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
    if (strlen($new_value) && !core::validate_hex_color($new_value)) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains incorrect value!',
        'The color should be specified in a special format.'], ['title' => translation::get($field->title)]
      ));
    } else {
      return true;
    }
  }

}}