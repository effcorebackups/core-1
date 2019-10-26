<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait field_validate_min_max {

  static function validate_min($field, $form, $element, &$new_value) {
    $min = $field->min_get();
    if (strlen($new_value) && $min && $new_value < $min) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains incorrect value!',
        'Field value is less than %%_value.'], ['title' => translation::get($field->title), 'value' => $min]
      ));
    } else {
      return true;
    }
  }

  static function validate_max($field, $form, $element, &$new_value) {
    $max = $field->max_get();
    if (strlen($new_value) && $max && $new_value > $max) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains incorrect value!',
        'Field value is more than %%_value.'], ['title' => translation::get($field->title), 'value' => $max]
      ));
    } else {
      return true;
    }
  }

}}