<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_number extends field_text {

  const input_min_number = -10000000000;
  const input_max_number = +10000000000;

  public $title = 'Number';
  public $attributes = ['x-type' => 'number'];
  public $element_attributes_default = [
    'type'     => 'number',
    'name'     => 'number',
    'required' => 'required',
    'min'      => self::input_min_number,
    'max'      => self::input_max_number,
    'step'     => 1,
    'value'    => 0
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
      $result = static::validate_required ($field, $form, $npath, $element, $new_value) &&
                static::validate_minlength($field, $form, $npath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $npath, $element, $new_value) &&
                static::validate_value    ($field, $form, $npath, $element, $new_value) &&
                static::validate_min      ($field, $form, $npath, $element, $new_value) &&
                static::validate_max      ($field, $form, $npath, $element, $new_value) &&
                static::validate_step     ($field, $form, $npath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $npath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : (float)self::input_min_number;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : (float)self::input_max_number;}

  static function validate_value($field, $form, $npath, $element, &$new_value) {
    if (core::validate_number($new_value) === false) {
      $form->add_error($npath.'/element',
        translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
        translation::get('Field value is not a valid number.')
      );
    } else {
      return true;
    }
  }

  static function validate_step($field, $form, $npath, $element, &$new_value) {
    if (strlen($new_value)) {
      $step = $element->attribute_select('step') ?: 1;
      $min = static::value_min_get($element);
      $max = static::value_max_get($element);
      if ((int)round(($min - $new_value) / $step, 5) !=
               round(($min - $new_value) / $step, 5)) {
        $form->add_error($npath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]).br.
          translation::get('Field value is not in valid range.')
        );
        return;
      }
    }
    return true;
  }

}}