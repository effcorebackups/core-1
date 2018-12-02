<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime extends field_text {

  const input_min_T_datetime = '0000-01-01T00:00:00';
  const input_max_T_datetime = '9999-12-30T23:59:59';

  public $is_return_native = false;
  public $title = 'Date/Time';
  public $attributes = ['data-type' => 'datetime-local'];
  public $element_attributes_default = [
    'type'     => 'datetime-local',
    'name'     => 'datetime',
    'required' => 'required',
    'min'      => self::input_min_T_datetime,
    'max'      => self::input_max_T_datetime
  ];

  function build() {
    parent::build();
    $value = parent::value_get();
    $element = $this->child_select('element');
    $min = $element->attribute_select('min');
    $max = $element->attribute_select('max');
    if ($min) $element->attribute_insert('min', locale::datetime_T_utc_to_T_loc($min  ));
    if ($max) $element->attribute_insert('max', locale::datetime_T_utc_to_T_loc($max  ));
    if ($value != null)       {$this->value_set(locale::datetime_T_utc_to_T_loc($value));                 return;}
    if ($value == null)       {$this->value_set(locale::datetime_T_utc_to_T_loc(core::T_datetime_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if ($this->is_return_native == true && core::validate_T_datetime($value)) return   core::sanitize_T_datetime  ($value);
    if ($this->is_return_native != true && core::validate_T_datetime($value)) return locale::datetime_T_loc_to_utc($value);
    return $value;
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