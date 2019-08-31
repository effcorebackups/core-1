<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime_local extends field_datetime {

  const input_min_datetime = '0000-01-01 12:00:00';
  const input_max_datetime = '9999-12-31 09:00:00';

  public $is_return_utc = true;
  public $title = 'Local Date/Time';
  public $attributes = ['data-type' => 'datetime-local'];
  public $element_attributes = [
    'type'     => 'datetime-local',
    'name'     => 'datetime_local',
    'required' => true,
    'min'      => self::input_min_datetime,
    'max'      => self::input_max_datetime
  ];

  function build() {
    if (!$this->is_builded) {
      field_text::build();
      $value = field_text::value_get();
      $min = $this->min_get();
      $max = $this->max_get();
      if ($min          ) {$this->  min_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $min        )));        }
      if ($max          ) {$this->  max_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $max        )));        }
      if ($value != null) {$this->value_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(       $value       ))); return;}
      if ($value == null) {$this->value_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(core::datetime_get()))); return;}
      $this->is_builded = true;
    }
  }

  function value_get() {
    $value = field_text::value_get();
    if ($this->is_return_utc != true && core::validate_T_datetime($value)) return                             core::T_datetime_to_datetime($value);
    if ($this->is_return_utc == true && core::validate_T_datetime($value)) return locale::datetime_loc_to_utc(core::T_datetime_to_datetime($value));
    return $value;
  }

}}