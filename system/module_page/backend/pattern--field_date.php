<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_text {

  const input_min_date = '0001-01-01';
  const input_max_date = '9999-12-31';

  public $is_local = false;
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
    $this->attribute_insert('value', core::date_get(), 'element_attributes_default');
    parent::build();
  }

  function value_get() {
    $value = parent::value_get();
    if ($this->is_local) return locale::global_date($value);
    else                 return $value;
  }

  function value_set($value) {
    if ($this->is_local) parent::value_set(locale::format_date($value));
    else                 parent::value_set($value);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function value_min_get($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_date;}
  static function value_max_get($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_date;}

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
    if (!core::validate_date($new_value)) {
      $field->error_set(
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}