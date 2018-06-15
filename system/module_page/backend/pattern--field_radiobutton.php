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

  function checked_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('checked') == 'checked';
  }

  function checked_set($is_checked = true) {
    $element = $this->child_select('element');
    if ($is_checked) $element->attribute_insert('checked', 'checked');
    else             $element->attribute_delete('checked');
  }

  function value_get() {
    $element = $this->child_select('element');
    return $element->attribute_select('value');
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
      $field->checked_set(in_array($field->value_get(), $new_values));
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