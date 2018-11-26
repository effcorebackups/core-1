<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_text {

  const input_min_date = '0001-01-01';
  const input_max_date = '9999-12-31';

  public $is_native = false;
  public $title = 'Date';
  public $attributes = ['data-type' => 'date'];
  public $element_attributes_default = [
    'type'     => 'date',
    'name'     => 'date',
    'required' => 'required',
    'min'      => self::input_min_date,
    'max'      => self::input_max_date
  ];

  function build() {
    parent::build();
    $value = parent::value_get();
    if ($value         && $this->is_native == false && core::validate_date_global($value)) {$this->value_set(  core::sanitize_date_global          ($value) ); return;}
    if ($value         && $this->is_native == false && core::validate_date_native($value)) {$this->value_set(locale::date_native_to_global         ($value) ); return;}
    if ($value         && $this->is_native          && core::validate_date_global($value)) {$this->value_set(locale::date_global_to_native         ($value) ); return;}
    if ($value         && $this->is_native          && core::validate_date_native($value)) {$this->value_set(  core::sanitize_date_native          ($value) ); return;}
    if ($value == null && $this->is_native == false                                      ) {$this->value_set(                              core::date_get() ); return;}
    if ($value == null && $this->is_native                                               ) {$this->value_set(locale::date_global_to_native(core::date_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if ($this->is_native && core::validate_date_native($value))
         return locale::date_native_to_global($value);
    else return $value;
  }

  function value_set($value) {
    if ($this->is_native == false && core::validate_date_global($value)) {parent::value_set(core::sanitize_date_global($value)); return;}
    if ($this->is_native          && core::validate_date_native($value)) {parent::value_set(core::sanitize_date_native($value)); return;}
    parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_date;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_date;}

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = static::request_value_get($name, static::cur_number_get($name), $form->source_get());
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
    if (!(($field->is_native          && core::validate_date_native($new_value)) ||
          ($field->is_native == false && core::validate_date_global($new_value)))) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}