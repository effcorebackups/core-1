<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_text {

  const input_min_date = '0001-01-01';
  const input_max_date = '9999-12-31';

  public $title = 'Date';
  public $attributes = ['x-type' => 'date'];
  public $element_attributes_default = [
    'type'     => 'date',
    'name'     => 'date',
    'required' => 'required',
    'min'      => self::input_min_date,
    'max'      => self::input_max_date
  ];

  function build() {
    $this->attribute_insert('value', core::date_get(), 'element_attributes_default');
    parent::build();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_date;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_date;}

  static function validate($field, $form) {
    $element = $field->child_select('element');
    $name = $field->element_name_get();
    $type = $field->element_type_get();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::cur_index_get($name);
      $new_value = static::new_value_get($name, $cur_index, $form->source_get());
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_value    ($field, $form, $element, $new_value) &&
                static::validate_min      ($field, $form, $element, $new_value) &&
                static::validate_max      ($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (!core::validate_date($new_value)) {
      $field->error_add(
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}