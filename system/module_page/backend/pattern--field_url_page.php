<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url_page extends field_url {

  public $element_attributes = [
    'type'      => 'url',
    'name'      => 'url',
    'required'  => true,
    'minlength' => 1,
    'maxlength' => 255
  ];

  function render_description() {
    $this->description = [
      new text('Field value should be start with "%%_value".', ['value' => '/'       ]), br,
      new text('Field value cannot be start with "%%_value".', ['value' => '/manage/']), br,
      new text('Field value cannot be start with "%%_value".', ['value' => '/user/'  ])];
    return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if ( (strlen($new_value) &&  core::sanitize_url(         $new_value) != $new_value) ||
         (strlen($new_value) && !core::validate_url((new url($new_value))->full_get())) || 
         (strlen($new_value) && preg_match('%^/manage$|^/manage/.*$|^/user$|^/user/.*$|^[^/].*$%', $new_value))) {
      $field->error_set(
        'Field "%%_title" contains an incorrect URL!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}