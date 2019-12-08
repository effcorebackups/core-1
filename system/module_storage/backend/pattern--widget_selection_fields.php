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
      $widgets_group = new markup('x-widgets-group', ['data-has-rearrangeable' => 'true']);
      foreach ($this->fields as $c_id => $c_info) {
        $c_widget_manage = new widget_selection_field_manage($c_info->entity_name, $c_info->entity_field_name);
        $c_widget_manage->build();
        $widgets_group->child_insert($c_widget_manage, $c_id);}
      $widget_insert = new widget_selection_field_insert;
      $widget_insert->build();
      $this->child_insert($widgets_group, 'widgets_group');
      $this->child_insert($widget_insert, 'widget_insert');
      $this->is_builded = true;
    }
  }

}}