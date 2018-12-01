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
    if ($value && core::validate_date($value)) {$this->value_set(locale::date_utc_to_loc($value));           return;}
    if ($value == null                       ) {$this->value_set(locale::date_utc_to_loc(core::date_get())); return;}
  }

  function value_get() {
    $value = parent::value_get();
    if (core::validate_date($value))
         return locale::date_loc_to_utc($value);
    else return $value;
  }

  function value_set($value) {
    if (core::validate_date($value))
         parent::value_set(core::sanitize_date($value));
    else parent::value_set($value);
  }

  function render_description_min($element) {return new markup('p', ['class' => ['min' => 'min']], translation::get('Minimum field value: %%_value.', ['value' => static::value_min_get($this->child_select('element'))]));}
  function render_description_max($element) {return new markup('p', ['class' => ['max' => 'max']], translation::get('Maximum field value: %%_value.', ['value' => static::value_max_get($this->child_select('element'))]));}

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {$min = $element->attribute_select('min') ?: self::input_min_date; return locale::date_utc_to_loc($min);}
  static function value_max_get($element) {$max = $element->attribute_select('max') ?: self::input_max_date; return locale::date_utc_to_loc($max);}

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
    if (strlen($new_value) && !core::validate_date($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      $new_value = core::sanitize_date($new_value);
      return true;
    }
  }

}}