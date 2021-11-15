<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_timezone extends field_select {

  public $title = 'Time zone';
  public $title__not_selected = '- select -';
  public $sort = 'by_zones'; # by_zones | by_names
  public $attributes = ['data-type' => 'timezone'];
  public $element_attributes = [
    'name'     => 'timezone',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $this->option_insert($this->title__not_selected, 'not_selected');
      if ($this->sort === 'by_zones') $list = static::list_get_by_zones();
      if ($this->sort === 'by_names') $list = static::list_get_by_names();
      foreach ($list as $c_name => $c_title)
        $this->option_insert($c_title, $c_name);
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function list_get_by_zones() {
    $result = [];
    $buffer = [];
    foreach (\DateTimeZone::listIdentifiers() as $c_zone) {
      $c_offset = core::timezone_get_offset_tme($c_zone);
      $buffer[str_replace([':'     ], [''        ], $c_offset)][$c_zone] =
              str_replace(['-'     ], ['−'       ], $c_offset).' — '.
              str_replace(['_', '/'], ['-', ' / '], $c_zone  );
    }
    krsort($buffer, SORT_NUMERIC);
    foreach ($buffer as $c_zone_group) {
      asort($c_zone_group);
      foreach ($c_zone_group as $c_zone => $c_title) {
        $result[$c_zone] = $c_title;
      }
    }
    return $result;
  }

  static function list_get_by_names() {
    $result = [];
    foreach (\DateTimeZone::listIdentifiers() as $c_zone) {
      $c_offset = core::timezone_get_offset_tme($c_zone);
      $result[$c_zone] = str_replace(['_', '/'], ['-', ' / '], $c_zone).' ('.$c_offset.')';
    }
    return $result;
  }

}}