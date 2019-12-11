<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_text extends field {

  public $title = 'Text';
  public $attributes = ['data-type' => 'text'];
  public $element_attributes = [
    'type'      => 'text',
    'name'      => 'text',
    'required'  => true,
    'maxlength' => 255];
# ─────────────────────────────────────────────────────────────────────
  public $is_validate_uniqueness = false;

  ###########################
  ### static declarations ###
  ###########################

  static function on_validate($field, $form, $npath) {
    $element = $field->child_select('element');
    $name = $field->name_get();
    $type = $field->type_get();
    if ($name && $type) {
      if ($field->disabled_get()) return true;
      if ($field->readonly_get()) return true;
      $new_value = static::request_value_get($name, static::current_number_get($name), $form->source_get());
      $old_value = $field->value_get_initial();
      $result = static::validate_required  ($field, $form, $element, $new_value) &&
                static::validate_minlength ($field, $form, $element, $new_value) &&
                static::validate_maxlength ($field, $form, $element, $new_value) &&
                static::validate_value     ($field, $form, $element, $new_value) &&
                static::validate_pattern   ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                static::validate_uniqueness($field, $new_value,      $old_value) : true);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_required($field, $form, $element, &$new_value) {
    if ($field->required_get() && strlen($new_value) == 0) {
      $field->error_set(
        'Field "%%_title" can not be blank!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

  static function validate_minlength($field, $form, $element, &$new_value) {
    $minlength = $field->minlength_get();
    if (strlen($new_value) && is_numeric($minlength) && $minlength > strlen($new_value)) {
      $field->error_set(
        'Field "%%_title" must contain a minimum of %%_number character%%_plural{number,s}!', ['title' => translation::get($field->title), 'number' => $minlength]
      );
    } else {
      return true;
    }
  }

  static function validate_maxlength($field, $form, $element, &$new_value) {
    $maxlength = $field->maxlength_get();
    if (strlen($new_value) && is_numeric($maxlength) && $maxlength < strlen($new_value)) {
      $new_value = substr($new_value, 0, $maxlength);
      $field->error_set(new text_multiline([
        'Field "%%_title" must contain a maximum of %%_number character%%_plural{number,s}!',
        'Value was trimmed to the required length!',
        'Check field again before submit.'], ['title' => translation::get($field->title), 'number' => $maxlength]
      ));
    } else {
      return true;
    }
  }

  static function validate_pattern($field, $form, $element, &$new_value) {
    $pattern = $field->pattern_get();
    if (strlen($new_value) && $pattern &&
              !preg_match('%'.$pattern.'%', $new_value)) {
      $field->error_set(new text_multiline([
        'Field "%%_title" contains incorrect value!',
        'Field value does not match the regular expression %%_expression.'], ['title' => translation::get($field->title), 'expression' => $pattern]
      ));
    } else {
      return true;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    return true;
  }

  static function validate_uniqueness($field, $new_value, $old_value = null) {
    $result = $field->value_is_unique_in_storage_sql($new_value);
    if ((strlen($old_value) == 0 && $result instanceof instance                                                      ) || # insert new value
        (strlen($old_value) != 0 && $result instanceof instance && $result->{$field->entity_field_name} != $old_value)) { # update old value
      $field->error_set(new text_multiline([
        'Field "%%_title" contains the previously used value!',
        'Only unique value is allowed.'], ['title' => translation::get($field->title)]
      ));
    } else {
      return true;
    }
  }

}}