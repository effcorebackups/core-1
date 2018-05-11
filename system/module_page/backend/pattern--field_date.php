<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_date extends field_text {

  const input_min_date = '0001-01-01';
  const input_max_date = '9999-12-31';

  public $title = 'Date';
  public $attributes = ['x-type' => 'date'];
  public $element_attributes_default = [
    'type'     => 'date',
    'name'     => 'date',
    'required' => 'required',
    'min'      => self::input_min_date,
    'max'      => self::input_max_date
  ];

  function build() {
    $this->attribute_insert('value', factory::date_get(), 'element_attributes_default');
    parent::build();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function get_min_value($element) {return $element->attribute_select('min') !== null ? $element->attribute_select('min') : self::input_min_date;}
  static function get_max_value($element) {return $element->attribute_select('max') !== null ? $element->attribute_select('max') : self::input_max_date;}

  static function validate($field, $form, $dpath) {
    $element = $field->child_select('element');
    $name = $field->get_element_name();
    $type = $field->get_element_type();
    if ($name && $type) {
      if (static::is_disabled($field, $element)) return true;
      if (static::is_readonly($field, $element)) return true;
      $cur_index = static::get_cur_index($name);
      $new_value = static::get_new_value($name, $cur_index);
      $result = static::validate_required ($field, $form, $dpath, $element, $new_value) &&
                static::validate_minlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_maxlength($field, $form, $dpath, $element, $new_value) &&
                static::validate_value    ($field, $form, $dpath, $element, $new_value) &&
                static::validate_min      ($field, $form, $dpath, $element, $new_value) &&
                static::validate_max      ($field, $form, $dpath, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $dpath, $element, $new_value);
      $element->attribute_insert('value', $new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $dpath, $element, &$new_value) {
    if (strlen($new_value) && (
       !preg_match('%^(?<Y>[0-9]{4})-(?<m>[0-1][0-9])-(?<d>[0-3][0-9])$%', $new_value, $matches) ||
       !checkdate($matches['m'],
                  $matches['d'],
                  $matches['Y']))) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains an incorrect date!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}