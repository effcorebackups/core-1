<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_radiobutton extends field {

  public $title = 'Radiobutton';
  public $title_position = 'bottom';
  public $attributes = ['x-type' => 'radiobutton'];
  public $element_attributes_default = [
    'type' => 'radio',
    'name' => 'radio'
  ];

  function value_get($default = false) {
    $element = $this->child_select('element');
    return $element->attribute_select('checked') == 'checked' || $default ?
           $element->attribute_select('value') : '';
  }

  function value_set($value) {
    $element = $this->child_select('element');
    if  ($element->attribute_select('value') == $value)
         $element->attribute_insert('checked', 'checked');
    else $element->attribute_delete('checked');
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      $new_values = static::new_values_get($name);
      $result = static::validate_required($field, $form, $npath, $element, $new_values);
      $value_default = $field->value_get(true);
      $field->value_set(in_array($value_default, $new_values) ?
                                 $value_default : '');
      return $result;
    }
  }

  static function validate_required($field, $form, $npath, $element, &$new_values) {
    if ($element->attribute_select('required') && !core::in_array_string_compare($element->attribute_select('value'), $new_values)) {
      $form->error_add($npath.'/element',
        translation::get('Field "%%_title" must be checked!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}