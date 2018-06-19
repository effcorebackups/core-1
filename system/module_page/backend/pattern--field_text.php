<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_text extends field {

  public $title = 'Text';
  public $attributes = ['x-type' => 'text'];
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

  static function validate($field, $form) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::cur_index_get($name);
      $new_value = static::request_value_get($name, $cur_index, $form->source_get());
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
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $field->error_add(
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_minlength($field, $form, $element, &$new_value) {
    if ($element->attribute_select('minlength') &&
        $element->attribute_select('minlength') > strlen($new_value) && strlen($new_value)) {
      $field->error_add(
        translation::get('Field "%%_title" must contain a minimum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('minlength')])
      );
    } else {
      return true;
    }
  }

  static function validate_maxlength($field, $form, $element, &$new_value) {
    if ($element->attribute_select('maxlength') &&
        $element->attribute_select('maxlength') < strlen($new_value)) {
      $new_value = substr($new_value, 0, $element->attribute_select('maxlength'));
      $field->error_add(
        translation::get('Field "%%_title" must contain a maximum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('maxlength')]).br.
        translation::get('Value was trimmed to the required length!').br.
        translation::get('Check field again before submit.')
      );
    } else {
      return true;
    }
  }

  static function validate_min($field, $form, $element, &$new_value) {
    $min = static::value_min_get($element);
    if (strlen($new_value) && $new_value < $min) {
      $field->error_add(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is less than %%_value.', ['value' => $min])
      );
    } else {
      return true;
    }
  }

  static function validate_max($field, $form, $element, &$new_value) {
    $max = static::value_max_get($element);
    if (strlen($new_value) && $new_value > $max) {
      $field->error_add(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is more than %%_value.', ['value' => $max])
      );
    } else {
      return true;
    }
  }

  static function validate_pattern($field, $form, $element, &$new_value) {
    if (strlen($new_value) && $element->attribute_select('pattern') &&
                  !preg_match($element->attribute_select('pattern'), $new_value)) {
      $field->error_add(
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value does not match the regular expression %%_expression.', ['expression' => $element->attribute_select('pattern')])
      );
    } else {
      return true;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    return true;
  }

}}