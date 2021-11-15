<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field_text {

  use field__shared;

  const input_min_number = -10000000000;
  const input_max_number = +10000000000;

  public $title = 'Digit';
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

  static function on_validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = request::value_get($name, static::current_number_generate($name), $form->source_get());
      $new_value = str_replace(',', '.', $new_value);
      $old_value = $field->value_get_initial();
      $result = static::validate_required       ($field, $form, $element, $new_value) &&
                static::validate_minlength      ($field, $form, $element, $new_value) &&
                static::validate_maxlength      ($field, $form, $element, $new_value) &&
                static::validate_value          ($field, $form, $element, $new_value) &&
                static::validate_min            ($field, $form, $element, $new_value) &&
                static::validate_max            ($field, $form, $element, $new_value) &&
                static::validate_fractional_part($field, $form, $element, $new_value) &&
                static::validate_range          ($field, $form, $element, $new_value) &&
                static::validate_pattern        ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                static::validate_uniqueness     ($field,                  $new_value, $old_value) : true);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && core::validate_number($new_value) === false) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Field value is not a valid number.'], ['title' => (new text($field->title))->render() ]
      ));
    } else {
      return true;
    }
  }

  static function validate_fractional_part($field, $form, $element, &$new_value) {
    if (strlen($new_value)) {
      $str_stp = core::format_number($field->step_get() ?: 1, core::fpart_max_len);
      $fp_stp_length = core::fractional_part_length_get($str_stp,   false);
      $fp_val_length = core::fractional_part_length_get($new_value, false);
      if ($fp_val_length > core::fpart_max_len ||
          $fp_val_length > $fp_stp_length) {
        $field->error_set(new text_multiline([
          'Field "%%_title" contains an error!',
          'Fractional part is too long.'], ['title' => (new text($field->title))->render() ]
        ));
        return;
      }
    }
    return true;
  }

  static function validate_range($field, $form, $element, &$new_value) {
    if (strlen($new_value)) {
      $str_min = core::format_number($field-> min_get(),      core::fpart_max_len);
      $str_max = core::format_number($field-> max_get(),      core::fpart_max_len);
      $str_stp = core::format_number($field->step_get() ?: 1, core::fpart_max_len);
      if (!core::validate_range($str_min, $str_max, $str_stp, $new_value)) {
        $field->error_set(new text_multiline([
          'Field "%%_title" contains an error!',
          'Field value is not in valid range.'], ['title' => (new text($field->title))->render() ]
        ));
        return;
      }
    }
    return true;
  }

}}