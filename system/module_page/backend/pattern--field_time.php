<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_time extends field_text {

  const input_min_time = '00:00:00';
  const input_max_time = '23:59:59';

  public $title = 'Time';
  public $attributes = ['data-type' => 'time'];
  public $element_attributes_default = [
    'type'     => 'time',
    'name'     => 'time',
    'required' => 'required',
    'min'      => self::input_min_time,
    'max'      => self::input_max_time,
    'step'     => 60
  ];

  function build() {
    parent::build();
    $value = parent::value_get();
    if ($value != null) {$this->value_set($value);                                    return;}
    if ($value == null) {$this->value_set(locale::time_utc_to_loc(core::time_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if (core::validate_time($value))
         return core::sanitize_time($value);
    else return $value;
  }

  function value_set($value) {
    if (core::validate_time($value))
         parent::value_set(core::sanitize_time($value));
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
      $new_value = strlen($new_value) == 5 ? $new_value.':00' : $new_value;
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
    if (strlen($new_value) && !core::validate_time($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect time!', ['title' => translation::get($field->title)])
      );
    } else {
      $new_value = core::sanitize_time($new_value);
      return true;
    }
  }

}}