<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_text {

  const input_min_date = '0000-01-01';
  const input_max_date = '9999-12-31';

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
    if ($value && core::validate_date_global($value)) {$this->value_set(locale::date_global_to_native($value,           false)); return;}
    if ($value == null                              ) {$this->value_set(locale::date_global_to_native(core::date_get(), false)); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if (core::validate_date_global($value))
         return locale::date_native_to_global($value, false);
    else return $value;
  }

  function value_set($value) {
    if (core::validate_date_global($value))
         parent::value_set(core::sanitize_date_global($value));
    else parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? locale::date_global_to_native($element->attribute_select('min'), false) : locale::date_global_to_native(self::input_min_date, false);}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? locale::date_global_to_native($element->attribute_select('max'), false) : locale::date_global_to_native(self::input_max_date, false);}

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
    if (strlen($new_value) && !core::validate_date_global($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      $new_value = core::sanitize_date_global($new_value);
      return true;
    }
  }

}}