<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_datetime_local extends field_datetime {

  const input_min_datetime = '0000-01-01 12:00:00';
  const input_max_datetime = '9999-12-31 09:00:00';

  public $is_get_utc = true;
  public $is_set_utc = true;
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
      if ($min          ) {$this->  min_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $min        )));                                  }
      if ($max          ) {$this->  max_set(core::datetime_to_T_datetime(locale::datetime_utc_to_loc(        $max        )));                                  }
      if ($value != null) {$this->value_set(core::datetime_to_T_datetime(                                   $value       ) ); $this->is_builded = true; return;}
      if ($value == null) {$this->value_set(core::datetime_to_T_datetime(                            core::datetime_get()) ); $this->is_builded = true; return;}
    }
  }

  function value_get() {
    $value = field_text::value_get();
    if ($this->is_get_utc == true && core::validate_T_datetime($value)) return locale::datetime_loc_to_utc(core::T_datetime_to_datetime($value));
    if ($this->is_get_utc != true && core::validate_T_datetime($value)) return                             core::T_datetime_to_datetime($value);
    return $value;
  }

  function value_set($value) {
    if     (core::validate_T_datetime($value) && $this->is_set_utc == true) parent::value_set(locale::datetime_T_utc_to_T_loc(core::   sanitize_T_datetime($value)));
    elseif (core::validate_T_datetime($value) && $this->is_set_utc != true) parent::value_set(                                core::   sanitize_T_datetime($value) );
    elseif (core::validate_datetime  ($value) && $this->is_set_utc == true) parent::value_set(locale::datetime_T_utc_to_T_loc(core::datetime_to_T_datetime($value)));
    elseif (core::validate_datetime  ($value) && $this->is_set_utc != true) parent::value_set(                                core::datetime_to_T_datetime($value) );
    else                                                                    parent::value_set                                                             ($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($field, $form, $npath) {
    $is_set_utc_old = $field->is_set_utc;
    $field->is_set_utc = false;
    $result = parent::validate($field, $form, $npath);
    $field->is_set_utc = $is_set_utc_old;
    return $result;
  }

}}