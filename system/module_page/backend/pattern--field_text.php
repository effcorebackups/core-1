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
      $index = !isset(static::$indexes[$name]) ?
                     (static::$indexes[$name] = 0) :
                    ++static::$indexes[$name];
      if (!static::validate_disabled($element)) return false;
      if (!static::validate_readonly($element)) return false;
      return true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $indexes = [];

  static function validate_disabled($element) {
    return $element->attribute_select('disabled') ? false : true;
  }

  static function validate_readonly($element) {
    return $element->attribute_select('readonly') ? false : true;
  }

}}