<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_textarea extends form_field {

  public $title = 'Textarea';

  function build() {
    $value = $this->attribute_select('value', 'element_attributes') ?: '';
    $this->attribute_delete('value', 'element_attributes');
    $this->child_insert(new markup('textarea', [
      'name'      => 'textarea',
      'required'  => 'required',
      'rows'      => 5,
      'minlength' => 5,
      'maxlength' => 255,
    ], ['content' => new text($value)]), 'element');
    parent::build();
  }

}}