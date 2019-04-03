<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_nick extends field_text {

  const allowed_characters = 'a-zA-Z0-9_\\-';
  const allowed_characters_title = '"a-z", "A-Z", "0-9", "_", "-"';

  public $title = 'Nick';
  public $attributes = ['data-type' => 'nick'];
  public $description = 'Field can contain only the next characters: '.self::allowed_characters_title.'.';
  public $new_value_must_be_unique = true;
  public $element_attributes = [
    'data-type' => 'nick',
    'name'      => 'nick',
    'required'  => true,
    'minlength' => 4,
    'maxlength' => 32,
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
      $result = static::validate_required ($field, $form, $element, $new_value) &&
                static::validate_minlength($field, $form, $element, $new_value) &&
                static::validate_maxlength($field, $form, $element, $new_value) &&
                static::validate_value    ($field, $form, $element, $new_value) &&
                static::validate_pattern  ($field, $form, $element, $new_value);
      $field->value_set($new_value);
      return $result;
    }
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (strlen($new_value) && !core::validate_nick($new_value)) {
      $field->error_set(
        'Field "%%_title" contains incorrect value!', ['title' => translation::get($field->title)]
      );
    } else {
      if ($field->new_value_must_be_unique) {
        $storage = storage::get(entity::get('user')->storage_name);
        if ($storage->is_available()) {
          $user_by_nick = (new instance('user', [
            'nick' => $new_value
          ]))->select();
          if ($user_by_nick &&                                      # user with this nick is exists
              $user_by_nick->nick != $field->value_initial_get()) { # and this is another user
            $field->error_set(
              'User with this Nick was already registered!'
            );
          }
        } else {
          $field->error_set(
            'Field "%%_title" cannot access to the storage for check value on uniqueness!', ['title' => translation::get($field->title)]
          );
        }
      }  
      return true;
    }
  }

}}