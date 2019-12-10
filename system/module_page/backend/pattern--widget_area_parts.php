<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_parts extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area-manage'];
  public $id_area;
  public $presets;

  function __construct($id_area, $presets, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    $this->presets = $presets;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $c_weight = 0;
      $widgets_group = new markup('x-widgets-group', ['data-has-rearrangeable' => 'true']);
      foreach ($this->presets as $c_id_preset) {
        $c_widget_manage = new widget_area_part_manage($this->id_area, $c_id_preset, [], $c_weight);
        $c_widget_manage->build();
        $widgets_group->child_insert($c_widget_manage, $c_id_preset);
        $c_weight -= 5;}
      $widget_insert = new widget_area_part_insert($this->id_area);
      $widget_insert->build();
      $this->child_insert($widgets_group, 'widgets_group');
      $this->child_insert($widget_insert, 'widget_insert');
      $this->is_builded = true;
    }
  }

}}