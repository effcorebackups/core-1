<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url_page extends field_url {

  public $element_attributes = [
    'type'      => 'url',
    'name'      => 'url',
    'required'  => true,
    'minlength' => 1,
    'maxlength' => 2047
  ];

  public $should_be_included = ['path' => 'path'];
  public $should_be_excluded = [
    'protocol' => 'protocol',
    'domain'   => 'domain',
    'query'    => 'query',
    'anchor'   => 'anchor'];

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
    if (  parent::validate_value($field, $form, $element,  $new_value) === true  ) {
      if (strlen($new_value) && preg_match('%^[^/].*$%',               $new_value)) {$field->error_set('Field value should be start with "%%_value".', ['value' => '/'       ]); return;}
      if (strlen($new_value) && preg_match('%^/user$|^/user/.*$%',     $new_value)) {$field->error_set('Field value cannot be start with "%%_value".', ['value' => '/user/'  ]); return;}
      if (strlen($new_value) && preg_match('%^/manage$|^/manage/.*$%', $new_value)) {$field->error_set('Field value cannot be start with "%%_value".', ['value' => '/manage/']); return;}
      return true;
    }
  }

}}