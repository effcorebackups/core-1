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
                static::validate_maxlength($field, $form, $dpath, $element, $new_value);
      $new_values = strlen($new_value) ? explode(',', $new_value) : [];
      $result = $result && static::validate_multiple($field, $form, $dpath, $element, $new_values);
      $result = $result && static::validate_values  ($field, $form, $dpath, $element, $new_values);
      $element->attribute_insert('value', implode(',', $new_values));
      return $result;
    }
  }

  static function validate_multiple($field, $form, $dpath, $element, &$new_values) {
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $new_values = array_slice($new_values, -1);
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

  static function validate_values($field, $form, $dpath, $element, &$new_values) {
    foreach ($new_values as $c_value) {
      if (factory::validate_email($c_value) == false) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains an incorrect email address!', ['title' => translation::get($field->title)])
        );
        return;
      }
    }
    return true;
  }

}}