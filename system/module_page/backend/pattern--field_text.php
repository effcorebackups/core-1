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

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $dpath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($field, $form, $dpath, $element, $new_value) &&
                static::validate_minlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $dpath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  static function validate_required($field, $form, $dpath, $element, &$new_value) {
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_minlength($field, $form, $dpath, $element, &$new_value) {
    if ($element->attribute_select('minlength') &&
        $element->attribute_select('minlength') > strlen($new_value) && strlen($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a minimum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('minlength')])
      );
    } else {
      return true;
    }
  }

  static function validate_maxlength($field, $form, $dpath, $element, &$new_value) {
    if ($element->attribute_select('maxlength') &&
        $element->attribute_select('maxlength') < strlen($new_value)) {
      $new_value = substr($new_value, 0, $element->attribute_select('maxlength'));
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a maximum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('maxlength')]).br.
        translation::get('Value was trimmed to the required length!').br.
        translation::get('Check field again before submit.')
      );
    } else {
      return true;
    }
  }

  static function validate_min($field, $form, $dpath, $element, &$new_value) {
    $min = static::get_min_value($element);
    if (strlen($new_value) && $new_value < $min) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is less than %%_value.', ['value' => $min])
      );
    } else {
      return true;
    }
  }

  static function validate_max($field, $form, $dpath, $element, &$new_value) {
    $max = static::get_max_value($element);
    if (strlen($new_value) && $new_value > $max) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is more than %%_value.', ['value' => $max])
      );
    } else {
      return true;
    }
  }

  static function validate_pattern($field, $form, $dpath, $element, &$new_value) {
    if (strlen($new_value) && $element->attribute_select('pattern') &&
                  !preg_match($element->attribute_select('pattern'), $new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value does not match the regular expression %%_expression.', ['expression' => $element->attribute_select('pattern')])
      );
    } else {
      return true;
    }
  }


}}