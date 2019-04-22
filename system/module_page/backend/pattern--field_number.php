<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field_text {

  const input_min_number = -10000000000;
  const input_max_number = +10000000000;

  public $title = 'Number';
  public $attributes = ['data-type' => 'number'];
  public $element_attributes = [
    'type'     => 'number',
    'name'     => 'number',
    'required' => true,
    'min'      => self::input_min_number,
    'max'      => self::input_max_number,
    'step'     => 1,
    'value'    => 0
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
      $new_value = static::request_value_get($name, static::current_number_get($name), $form->source_get());
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_value    ($field, $form, $element, $new_value) &&
                static::validate_min      ($field, $form, $element, $new_value) &&
                static::validate_max      ($field, $form, $element, $new_value) &&
                static::validate_step     ($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && core::validate_number($new_value) === false) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains incorrect value!',
        'Field value is not a valid number.'], ['title' => translation::get($field->title)]
      ));
    } else {
      return true;
    }
  }

  static function validate_step($field, $form, $element, &$new_value) {
    if (strlen($new_value)) {
      $step = $field->step_get() ?: 1;
      $min = (float)$field->min_get();
      $max = (float)$field->max_get();
      if ((int)round(($min - $new_value) / $step, 5) !=
               round(($min - $new_value) / $step, 5)) {
        $field->error_set(new text_multiline([
          'Field "%%_title" contains incorrect value!',
          'Field value is not in valid range.'], ['title' => translation::get($field->title)]
        ));
        return;
      }
    }
    return true;
  }

}}