<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime_local extends field_datetime {

  const input_min_datetime = '0001-01-01 12:00:00';
  const input_max_datetime = '9999-12-31 09:00:00';

  public $title = 'Local Date/Time';
  public $attributes = ['data-type' => 'datetime-local'];
  public $element_attributes = [
    'type'     => 'datetime-local',
    'name'     => 'datetime_local',
    'min'      => self::input_min_datetime,
    'max'      => self::input_max_datetime,
    'required' => true,
    'step'     => 1];
  public $is_utc_conversion = true;

  function build() {
    if (!$this->is_builded) {
      field_text::build();
      $min = $this->min_get();
      $max = $this->max_get();
      if ($min) $this->min_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc($min)));
      if ($max) $this->max_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc($max)));
      $this->value_set(field_text::value_get());
    }
  }

  function value_get() {
    $value = parent::value_get();
    if ($value !== null && core::validate_datetime($value) && $this->is_utc_conversion === true) $value = locale::datetime_loc_to_utc($value);
    return $value;
  }

  function value_set($value) {
    $this->value_set_initial($value);
    if ($value === null && $this->value_current_if_null === true) $value = core::datetime_get();
    if ($value !== null && core::validate_datetime($value)) $value = core::datetime_to_T_datetime($value);
    if ($value !== null && core::validate_T_datetime($value) && $this->is_utc_conversion === true) $value = locale::datetime_T_utc_to_T_loc($value);
    parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_validate($field, $form, $npath) {
    $field->is_utc_conversion = false;
    $result = parent::on_validate($field, $form, $npath);
    $field->is_utc_conversion = true;
    return $result;
  }

  static function on_request_value_set($field, $form, $npath) {
    $field->is_utc_conversion = false;
    $result = parent::on_request_value_set($field, $form, $npath);
    $field->is_utc_conversion = true;
    return $result;
  }

}}