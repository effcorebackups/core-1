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

  function validate($form, $dpath) {
    $element = $this->child_select('element');
    $name = $this->get_element_name();
    $type = $this->get_element_type();
    if ($name && $type) {
      if (static::validate_is_disabled($this, $element)) return true;
      if (static::validate_is_readonly($this, $element)) return true;
      $index = !isset(static::$indexes[$name]) ?
                     (static::$indexes[$name] = 0) :
                    ++static::$indexes[$name];
      $new_value = !isset($_POST[$name]) ? '' :
               (is_string($_POST[$name]) ? $_POST[$name] : 
                (is_array($_POST[$name]) &&
                    isset($_POST[$name][$index]) ?
                          $_POST[$name][$index] : ''));
      $result = static::validate_text_required ($this, $element, $new_value, $form, $dpath) &&
                static::validate_text_minlength($this, $element, $new_value, $form, $dpath) &&
                static::validate_text_maxlength($this, $element, $new_value, $form, $dpath);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $indexes = [];

  static function validate_is_disabled($field, $element) {
    return $element->attribute_select('disabled') ? true : false;
  }

  static function validate_is_readonly($field, $element) {
    return $element->attribute_select('readonly') ? true : false;
  }

  static function validate_text_required($field, $element, &$new_value, $form, $dpath) {
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_text_minlength($field, $element, &$new_value, $form, $dpath) {
    if ($element->attribute_select('minlength') &&
        $element->attribute_select('minlength') > strlen($new_value) && strlen($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a minimum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('minlength')])
      );
    } else {
      return true;
    }
  }

  static function validate_text_maxlength($field, $element, &$new_value, $form, $dpath) {
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

}}