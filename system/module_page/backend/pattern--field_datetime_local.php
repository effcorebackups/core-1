<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime_local extends field_datetime {

  const input_min_datetime = '0000-01-01 12:00:00';
  const input_max_datetime = '9999-12-31 09:00:00';

  public $is_return_utc = true;
  public $title = 'Local Date/Time';
  public $attributes = ['data-type' => 'datetime-local'];
  public $element_attributes_default = [
    'type'     => 'datetime-local',
    'name'     => 'datetime_local',
    'required' => 'required',
    'min'      => self::input_min_datetime,
    'max'      => self::input_max_datetime
  ];

  function build() {
    field_text::build();
    $value = field_text::value_get();
    $element = $this->child_select('element');
    $min = $element->attribute_select('min');
    $max = $element->attribute_select('max');
    if ($min) $element->attribute_insert('min', core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $min        )));
    if ($max) $element->attribute_insert('max', core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $max        )));
    if ($value != null)       {$this->value_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(       $value       ))); return;}
    if ($value == null)       {$this->value_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(core::datetime_get()))); return;}
  }

  function value_get() {
    $value = field_text::value_get();
    if ($this->is_return_utc != true && core::validate_T_datetime($value)) return                             core::T_datetime_to_datetime($value);
    if ($this->is_return_utc == true && core::validate_T_datetime($value)) return locale::datetime_loc_to_utc(core::T_datetime_to_datetime($value));
    return $value;
  }

}}