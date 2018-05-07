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
      $element->attribute_insert('value', $new_value);
      if (!static::validate_text_required($this, $element, $new_value, $form, $dpath)) return false;
      return true;
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

  static function validate_text_required($field, $element, $new_value, $form, $dpath) {
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
      return;
    }
  }

}}