<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page_part_preset extends page_part {

  public $id;
  public $managing_group = 'Text';
  public $managing_title;
  public $in_areas;

  function page_part_make() {
    $preset = static::select($this->id);
    if ($preset) {
      $page_part = new page_part;
      foreach ($page_part as $c_key => $c_value)
        $page_part->{$c_key} =
           $preset->{$c_key};
      return $page_part;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('page_part_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::log_insert_about_duplicate('page_part_preset', $c_preset->id, $c_module_id);
          static::$cache[$c_preset->id] = $c_preset;
          static::$cache[$c_preset->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function select_all($id_area = null) {
    static::init();
    $result = static::$cache;
    if ($id_area)
      foreach ($result as $c_id => $c_preset)
        if (is_array(          $c_preset->in_areas) &&
           !in_array($id_area, $c_preset->in_areas))
          unset($result[$c_id]);
    return $result;
  }

  static function select($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

}}