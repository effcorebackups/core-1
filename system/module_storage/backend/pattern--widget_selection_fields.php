<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_fields extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'selection_fields'];
  public $fields = [];

  function __construct($fields, $attributes = [], $weight = 0) {
    $this->fields = $fields;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      foreach ($this->fields as $c_id => $c_info) {
        $c_field_manage = new widget_selection_field_manage($c_info->entity_name, $c_info->entity_field_name);
        $c_field_manage->build();
        $this->child_insert($c_field_manage, $c_id);}
      $field_insert = new widget_selection_field_insert;
      $field_insert->build();
      $this->child_insert($field_insert, 'field_insert');
      $this->is_builded = true;
    }
  }

}}