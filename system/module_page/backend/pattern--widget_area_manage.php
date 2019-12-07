<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_manage extends container {

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
        $c_part_manage = new widget_page_part_manage;
        $c_part_manage->id_area   = $this->id_area;
        $c_part_manage->id_preset = $c_id_preset;
        $c_part_manage->build();
        $this->child_insert($c_part_manage, 'part_manage_'.$c_id_preset);}
      $part_insert = new widget_page_part_insert;
      $part_insert->id_area = $this->id_area;
      $part_insert->build();
      $this->child_insert($part_insert, 'part_insert');
      $this->is_builded = true;
    }
  }

}}