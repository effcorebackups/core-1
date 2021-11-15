<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          trait field__shared {

  static function validate_min($field, $form, $element, &$new_value) {
    $min = $field->min_get();
    if (strlen($new_value) && strlen($min) && $new_value < $min) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains an error!',
        'Field value is less than %%_number.'], ['title' => (new text($field->title))->render(), 'number' => $min]
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
        'Field value is greater than %%_number.'], ['title' => (new text($field->title))->render(), 'number' => $max]
      ));
    } else {
      return true;
    }
  }

}}