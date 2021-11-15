<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url_page extends field_url {

  public $element_attributes = [
    'type'      => 'text',
    'name'      => 'url',
    'required'  => true,
    'maxlength' => 2048
  ];

  public $should_be_included = ['path' => 'path'];
  public $should_be_excluded = [
    'protocol' => 'protocol',
    'domain'   => 'domain',
    'query'    => 'query',
    'anchor'   => 'anchor'];

  function render_description() {
    $this->description = static::description_prepare($this->description);
    $this->description['url-page-root'   ] = new markup('p', ['data-id' => 'url-page-root'   ], new text('Field value should be start with "%%_value".', ['value' => '/'        ]));
    $this->description['url-page-manage' ] = new markup('p', ['data-id' => 'url-page-manage' ], new text('Field value cannot be start with "%%_value".', ['value' => '/manage/' ]));
    $this->description['url-page-user'   ] = new markup('p', ['data-id' => 'url-page-user'   ], new text('Field value cannot be start with "%%_value".', ['value' => '/user/'   ]));
    $this->description['url-page-dynamic'] = new markup('p', ['data-id' => 'url-page-dynamic'], new text('Field value cannot be start with "%%_value".', ['value' => '/dynamic/']));
    $this->description['url-page-modules'] = new markup('p', ['data-id' => 'url-page-modules'], new text('Field value cannot be start with "%%_value".', ['value' => '/modules/']));
    $this->description['url-page-readme' ] = new markup('p', ['data-id' => 'url-page-readme' ], new text('Field value cannot be start with "%%_value".', ['value' => '/readme/' ]));
    $this->description['url-page-shell'  ] = new markup('p', ['data-id' => 'url-page-shell'  ], new text('Field value cannot be start with "%%_value".', ['value' => '/shell/'  ]));
    $this->description['url-page-system' ] = new markup('p', ['data-id' => 'url-page-system' ], new text('Field value cannot be start with "%%_value".', ['value' => '/system/' ]));
    return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    if (  parent::validate_value($field, $form, $element,  $new_value) === true  ) {
      if (strlen($new_value) && preg_match('%^[^/].*$%',         $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value should be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/'        ])); return;}
      if (strlen($new_value) && preg_match('%^/manage(/.*|)$%',  $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/manage/' ])); return;}
      if (strlen($new_value) && preg_match('%^/user(/.*|)$%',    $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/user/'   ])); return;}
      if (strlen($new_value) && preg_match('%^/dynamic(/.*|)$%', $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/dynamic/'])); return;}
      if (strlen($new_value) && preg_match('%^/modules(/.*|)$%', $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/modules/'])); return;}
      if (strlen($new_value) && preg_match('%^/readme(/.*|)$%',  $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/readme/' ])); return;}
      if (strlen($new_value) && preg_match('%^/shell(/.*|)$%',   $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/shell/'  ])); return;}
      if (strlen($new_value) && preg_match('%^/system(/.*|)$%',  $new_value)) {$field->error_set(new text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new text($field->title))->render(), 'value' => '/system/' ])); return;}
      return true;
    }
  }

}}