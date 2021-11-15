<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_color extends field_text {

  public $title = 'Color';
  public $description = 'Color should be in the format "#abcdef", where "ab" is the value of the red component, "cd" — green and "ef" — blue.';
  public $attributes = ['data-type' => 'color'];
  public $element_attributes = [
    'type'     => 'color',
    'name'     => 'color',
    'value'    => '#ffffff',
    'required' => true
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function on_validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = request::value_get($name, static::current_number_generate($name), $form->source_get());
      $new_value = core::strtolower_en($new_value);
      $old_value = $field->value_get_initial();
      $result = static::validate_required  ($field, $form, $element, $new_value) &&
                static::validate_minlength ($field, $form, $element, $new_value) &&
                static::validate_maxlength ($field, $form, $element, $new_value) &&
                static::validate_value     ($field, $form, $element, $new_value) &&
                static::validate_pattern   ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                static::validate_uniqueness($field,                  $new_value, $old_value) : true);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_hex_color($new_value)) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Color should be in the format "#abcdef", where "ab" is the value of the red component, "cd" — green and "ef" — blue.'], ['title' => (new text($field->title))->render() ]
      ));
    } else {
      return true;
    }
  }

}}