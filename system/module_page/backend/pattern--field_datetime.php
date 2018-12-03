<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime extends field_text {

  const input_min_datetime = '0000-01-01 00:00:00';
  const input_max_datetime = '9999-12-31 00:00:00';

  public $title = 'Date/Time';
  public $attributes = ['data-type' => 'datetime'];
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
    $element = $this->child_select('element');
    $min = $element->attribute_select('min');
    $max = $element->attribute_select('max');
    if ($min) $element->attribute_insert('min', core::datetime_to_T_datetime(        $min        ));
    if ($max) $element->attribute_insert('max', core::datetime_to_T_datetime(        $max        ));
    if ($value != null)       {$this->value_set(core::datetime_to_T_datetime(       $value       )); return;}
    if ($value == null)       {$this->value_set(core::datetime_to_T_datetime(core::datetime_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if (core::validate_T_datetime($value))
         return core::T_datetime_to_datetime($value);
    else return $value;
  }

  function value_set($value) {
    if (core::validate_T_datetime($value))
         parent::value_set(core::sanitize_T_datetime($value));
    else parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = static::request_value_get($name, static::cur_number_get($name), $form->source_get());
      $new_value = strlen($new_value) == 16 ? $new_value.':00' : $new_value;
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
    if (strlen($new_value) && !core::validate_T_datetime($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date/time!', ['title' => translation::get($field->title)])
      );
    } else {
      $new_value = core::sanitize_T_datetime($new_value);
      return true;
    }
  }

}}