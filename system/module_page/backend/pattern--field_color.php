<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_color extends field_text {

  public $title = 'Color';
  public $description = 'The color should be in the format "#abcdef", where "ab" is the value of the red component, "cd" - green and "ef" - blue.';
  public $attributes = ['x-type' => 'color'];
  public $element_attributes_default = [
    'type'      => 'color',
    'name'      => 'color',
    'required'  => 'required',
    'value'     => '#ffffff'
  ];

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::cur_index_get($name);
      $new_value = static::new_value_get($name, $cur_index);
      $new_value = strtolower($new_value);
      $result = static::validate_required ($field, $form, $npath, $element, $new_value) &&
                static::validate_minlength($field, $form, $npath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $npath, $element, $new_value) &&
                static::validate_value    ($field, $form, $npath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $npath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $npath, $element, &$new_value) {
    if (!core::validate_hex_color($new_value)) {
      $form->error_add($npath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('The color should be specified in a special format.')
      );
    } else {
      return true;
    }
  }

}}