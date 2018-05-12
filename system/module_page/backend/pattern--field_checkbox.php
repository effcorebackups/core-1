<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_checkbox extends field {

  public $title = 'Checkbox';
  public $title_position = 'bottom';
  public $attributes = ['x-type' => 'checkbox'];
  public $element_attributes_default = [
    'type' => 'checkbox',
    'name' => 'checkbox'
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
      $new_values = static::get_new_value_multiple($name);
      $result = static::validate_required($field, $form, $dpath, $element, $new_values);
      if (factory::in_array_string_compare($element->attribute_select('value'), $new_values))
           $element->attribute_insert('checked', 'checked');
      else $element->attribute_delete('checked');
      return $result;
    }
  }

  static function validate_required($field, $form, $dpath, $element, &$new_values) {
    if ($element->attribute_select('required') && !factory::in_array_string_compare($element->attribute_select('value'), $new_values)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must be checked!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}