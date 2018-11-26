<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime_native extends field_text {

  const input_min_datetime = '0000-01-01 00:00:00';
  const input_max_datetime = '9999-12-31 23:59:59';

  public $title = 'Date/Time';
  public $attributes = ['data-type' => 'datetime-local'];
  public $element_attributes_default = [
    'type'     => 'datetime-local',
    'name'     => 'datetime',
    'required' => 'required',
    'min'      => self::input_min_datetime,
    'max'      => self::input_max_datetime
  ];

  function build() {
    parent::build();
    $value = parent::value_get();
    if ($value && core::validate_datetime_global($value)) {$this->value_set(locale::datetime_global_to_native              ($value)); return;}
    if ($value == null                                  ) {$this->value_set(locale::datetime_global_to_native(core::datetime_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if (core::validate_datetime_native($value))
         return locale::datetime_native_to_global($value);
    else return $value;
  }

  function value_set($value) {
    if (core::validate_datetime_native($value)) {parent::value_set(core::sanitize_datetime_native($value)); return;}
    parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_datetime;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_datetime;}

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
    if (!core::validate_datetime_native($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date/time!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}