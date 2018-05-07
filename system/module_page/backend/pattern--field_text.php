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
      if (static::is_disabled($this, $element)) return true;
      if (static::is_readonly($this, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($this, $element, $new_value, $form, $dpath) &&
                static::validate_minlength($this, $element, $new_value, $form, $dpath) &&
                static::validate_maxlength($this, $element, $new_value, $form, $dpath);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_required($field, $element, &$new_value, $form, $dpath) {
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" can not be blank!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_minlength($field, $element, &$new_value, $form, $dpath) {
    if ($element->attribute_select('minlength') &&
        $element->attribute_select('minlength') > strlen($new_value) && strlen($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a minimum of %%_num characters!', ['title' => translation::get($field->title), 'num' => $element->attribute_select('minlength')])
      );
    } else {
      return true;
    }
  }

  static function validate_maxlength($field, $element, &$new_value, $form, $dpath) {
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