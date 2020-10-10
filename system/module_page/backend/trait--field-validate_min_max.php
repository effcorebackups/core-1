<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait field_validate_min_max {

  static function validate_min($field, $form, $element, &$new_value) {
    $min = $field->min_get();
    if (strlen($new_value) && strlen($min) && $new_value < $min) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Field value is less than %%_number.'], ['title' => translation::apply($field->title), 'number' => $min]
      ));
    } else {
      return true;
    }
  }

  static function validate_max($field, $form, $element, &$new_value) {
    $max = $field->max_get();
    if (strlen($new_value) && strlen($max) && $new_value > $max) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Field value is greater than %%_number.'], ['title' => translation::apply($field->title), 'number' => $max]
      ));
    } else {
      return true;
    }
  }

}}