<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_selection_field_insert extends fieldset {

  public $tag_name = 'x-selection_field-insert';
  public $content_tag_name = null;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $options = ['not_selected' => '- no -'];
      $select_field = new field_select('Insert field');
      $select_field->values = $options;
      $select_field->build();
      $select_field->name_set('insert_field');
      $select_field->required_set(false);
      $button_insert = new button('', ['data-style' => 'narrow-insert']);
      $button_insert->build();
      $button_insert->value_set('button_insert');
      $this->child_insert($select_field,  'select');
      $this->child_insert($button_insert, 'button');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($group, $form, $npath) {
  }

  static function submit(&$group, $form, $npath) {
  }

}}