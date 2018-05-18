<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_email extends field_text {

  public $title = 'EMail';
  public $attributes = ['x-type' => 'email'];
  public $element_attributes_default = [
    'type'      => 'email',
    'name'      => 'email',
    'required'  => 'required',
    'minlength' => 5,
    'maxlength' => 64
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($field, $form, $npath, $element, $new_value) &&
                static::validate_minlength($field, $form, $npath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $npath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $npath, $element, $new_value) &&
                static::validate_multiple ($field, $form, $npath, $element, $new_value) &&
                static::validate_values   ($field, $form, $npath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  static function validate_multiple($field, $form, $npath, $element, &$new_value) {
    $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
    if (!$element->attribute_select('multiple') && count($multiple_values) > 1) {
      $new_value = array_pop($multiple_values);
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_values($field, $form, $npath, $element, &$new_value) {
    $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
    foreach ($multiple_values as $c_value) {
      if (core::validate_email($c_value) == false) {
        $form->add_error($npath.'/element',
          translation::get('Field "%%_title" contains an incorrect email address!', ['title' => translation::get($field->title)])
        );
        return;
      }
    }
    return true;
  }

}}