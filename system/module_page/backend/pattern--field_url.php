<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_url extends field_text {

  public $title = 'URL';
  public $attributes = ['data-type' => 'url'];
  public $element_attributes = [
    'type'      => 'url',
    'name'      => 'url',
    'required'  => true,
    'minlength' => 1,
    'maxlength' => 2047
  ];

  public $should_be_included = []; # protocol,domain,path,query,anchor
  public $should_be_excluded = []; # protocol,domain,path,query,anchor

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    $raw_url = new url($new_value, ['completion' => false]);
    if (strlen($new_value) && (new url($new_value))->has_error === true                                 ) {$field->error_set('Field "%%_title" contains an incorrect URL!', ['title' => (new text($field->title))->render() ]); return;}
    if (strlen($new_value) && isset($field->should_be_included['protocol']) && $raw_url->protocol === '') {$field->error_set('URL should contain protocol!'                                                                  ); return;}
    if (strlen($new_value) && isset($field->should_be_included['domain'  ]) && $raw_url->domain   === '') {$field->error_set('URL should contain domain!'                                                                    ); return;}
    if (strlen($new_value) && isset($field->should_be_included['path'    ]) && $raw_url->path     === '') {$field->error_set('URL should contain path!'                                                                      ); return;}
    if (strlen($new_value) && isset($field->should_be_included['query'   ]) && $raw_url->query    === '') {$field->error_set('URL should contain query!'                                                                     ); return;}
    if (strlen($new_value) && isset($field->should_be_included['anchor'  ]) && $raw_url->anchor   === '') {$field->error_set('URL should contain anchor!'                                                                    ); return;}
    if (strlen($new_value) && isset($field->should_be_excluded['protocol']) && $raw_url->protocol !== '') {$field->error_set('URL should not contain protocol!'                                                              ); return;}
    if (strlen($new_value) && isset($field->should_be_excluded['domain'  ]) && $raw_url->domain   !== '') {$field->error_set('URL should not contain domain!'                                                                ); return;}
    if (strlen($new_value) && isset($field->should_be_excluded['path'    ]) && $raw_url->path     !== '') {$field->error_set('URL should not contain path!'                                                                  ); return;}
    if (strlen($new_value) && isset($field->should_be_excluded['query'   ]) && $raw_url->query    !== '') {$field->error_set('URL should not contain query!'                                                                 ); return;}
    if (strlen($new_value) && isset($field->should_be_excluded['anchor'  ]) && $raw_url->anchor   !== '') {$field->error_set('URL should not contain anchor!'                                                                ); return;}
    return true;
  }

}}