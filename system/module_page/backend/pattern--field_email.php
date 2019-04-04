<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_email extends field_text {

  public $title = 'EMail';
  public $attributes = ['data-type' => 'email'];
  public $element_attributes = [
    'data-type' => 'email',
    'type'      => 'email',
    'name'      => 'email',
    'required'  => true,
    'minlength' => 5,
    'maxlength' => 64
  ];

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
      $new_value = strtolower($new_value);
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value) &&
                static::validate_multiple ($field, $form, $element, $new_value) &&
                static::validate_values   ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_multiple($field, $form, $element, &$new_value) {
    $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
    if (!$field->multiple_get() && count($multiple_values) > 1) {
      $new_value = array_pop($multiple_values);
      $field->error_set(
        'Field "%%_title" does not support multiple select!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

  static function validate_values($field, $form, $element, &$new_value) {
    $multiple_values = strlen($new_value) ? explode(',', $new_value) : [];
    foreach ($multiple_values as $c_value) {
      if (core::validate_email($c_value) == false) {
        $field->error_set(
          'Field "%%_title" contains an incorrect EMail address!', ['title' => translation::get($field->title)]
        );
        return;
      }
    }
    return true;
  }

  static function validate_uniqueness($field, $new_value, $old_value = null) {
    $user_by_email = (new instance('user', [
      'email' => strtolower($new_value)
    ]))->select();
    if (($user_by_email && $old_value === null                                       ) || # insert new email (e.g. registration)
        ($user_by_email && $old_value ==! null && $user_by_email->email != $old_value)) { # update old email
      $field->error_set(
        'User with this EMail was already registered!'
      );
    } else {
      return true;
    }
  }

}}