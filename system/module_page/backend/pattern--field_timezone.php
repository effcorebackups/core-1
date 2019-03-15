<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_timezone extends field_select {

  public $title = 'Timezone';
  public $sort = 'by_zones'; # by_zones | by_names
  public $attributes = ['data-type' => 'timezone'];
  public $element_attributes = [
    'name'     => 'timezone',
    'required' => true
  ];

  function build() {
    parent::build();
    $this->option_insert('- select -', 'not_selected');
    if ($this->sort == 'by_zones') $list = static::list_by_zones_get();
    if ($this->sort == 'by_names') $list = static::list_by_names_get();
    foreach ($list as $c_name => $c_title) {
      $this->option_insert($c_title, $c_name);
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function list_by_zones_get() {
    $result = [];
    foreach (\DateTimeZone::listIdentifiers() as $c_name) {
      $c_offset = core::timezone_offset_tme_get($c_name);
      $result[$c_name] = $c_offset.' — '.str_replace('/', ' / ', $c_name);
    }
    arsort($result, SORT_NUMERIC);
    return $result;
  }

  static function list_by_names_get() {
    $result = [];
    foreach (\DateTimeZone::listIdentifiers() as $c_name) {
      $c_offset = core::timezone_offset_tme_get($c_name);
      $result[$c_name] = str_replace('/', ' / ', $c_name).' ('.$c_offset.')';
    }
    return $result;
  }

}}