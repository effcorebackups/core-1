<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_text extends field {

  public $title = 'Text';
  public $attributes = ['data-type' => 'text'];
  public $element_attributes_default = [
    'type'      => 'text',
    'name'      => 'text',
    'required'  => 'required',
    'maxlength' => 255
  ];

  function value_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('value');
  }

  function value_set($value) {
    $element = $this->child_select('element');
    return $element->attribute_insert('value', $value);
  }

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
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_value    ($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_required($field, $form, $element, &$new_value) {
    if ($field->required_get() && strlen($new_value) == 0) {
      $field->error_set(
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_minlength($field, $form, $element, &$new_value) {
    $minlength = $field->minlength_get();
    if ($minlength &&
        $minlength > strlen($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" must contain a minimum of %%_number character%%_plural{number,s}!', ['title' => translation::get($field->title), 'number' => $minlength])
      );
    } else {
      return true;
    }
  }

  static function validate_maxlength($field, $form, $element, &$new_value) {
    $maxlength = $field->maxlength_get();
    if ($maxlength &&
        $maxlength < strlen($new_value)) {
      $new_value = substr($new_value, 0, $maxlength);
      $field->error_set(
        translation::get('Field "%%_title" must contain a maximum of %%_number character%%_plural{number,s}!', ['title' => translation::get($field->title), 'number' => $maxlength]).br.
        translation::get('Value was trimmed to the required length!').br.
        translation::get('Check field again before submit.')
      );
    } else {
      return true;
    }
  }

  static function validate_min($field, $form, $element, &$new_value) {
    $min = $field->min_get();
    if (strlen($new_value) && $min && $new_value < $min) {
      $field->error_set(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is less than %%_value.', ['value' => $min])
      );
    } else {
      return true;
    }
  }

  static function validate_max($field, $form, $element, &$new_value) {
    $max = $field->max_get();
    if (strlen($new_value) && $max && $new_value > $max) {
      $field->error_set(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is more than %%_value.', ['value' => $max])
      );
    } else {
      return true;
    }
  }

  static function validate_pattern($field, $form, $element, &$new_value) {
    $pattern = $field->pattern_get();
    if (strlen($new_value) && $pattern &&
              !preg_match('%'.$pattern.'%', $new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value does not match the regular expression %%_expression.', ['expression' => $pattern])
      );
    } else {
      return true;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    return true;
  }

}}