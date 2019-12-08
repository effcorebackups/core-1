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
      foreach ($this->presets as $c_id_preset) {
        $c_widget_manage = new widget_area_part_manage($this->id_area, $c_id_preset);
        $c_widget_manage->build();
        $this->child_insert($c_widget_manage, $c_id_preset);}
      $widget_insert = new widget_area_part_insert($this->id_area);
      $widget_insert->build();
      $this->child_insert($widget_insert, 'widget_area_part_insert');
      $this->is_builded = true;
    }
  }

}}