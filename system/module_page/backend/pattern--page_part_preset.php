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
  public $origin = 'nosql'; # nosql | dynamic

  function __construct($id = null, $managing_group = null, $managing_title = null, $in_areas = null, $display = null, $type = null, $source = null, $weight = 0) {
    if ($id)             $this->id             = $id;
    if ($managing_group) $this->managing_group = $managing_group;
    if ($managing_title) $this->managing_title = $managing_title;
    if ($in_areas)       $this->in_areas       = $in_areas;
    if ($display)        $this->display        = $display;
    if ($type)           $this->type           = $type;
    if ($source)         $this->source         = $source;
    parent::__construct($weight);
  }

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
  static protected $is_init_nosql   = false;
  static protected $is_init_dynamic = false;

  static function cache_cleaning() {
    static::$cache = null;
    static::$is_init_nosql   = false;
    static::$is_init_dynamic = false;
  }

  static function init() {
    if (!static::$is_init_nosql) {
         static::$is_init_nosql = true;
      foreach (storage::get('files')->select('page_part_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::log_insert_about_duplicate('page_part_preset', $c_preset->id, $c_module_id);
          static::$cache[$c_preset->id] = $c_preset;
          static::$cache[$c_preset->id]->module_id = $c_module_id;
          static::$cache[$c_preset->id]->origin = 'nosql';
        }
      }
    }
  }

  static function init_dynamic() {
    if (!static::$is_init_dynamic) {
         static::$is_init_dynamic = true;
      event::start('on_page_parts_init_dynamic');
    }
  }

  static function select_all($id_area = null, $with_dynamic = true) {
                       static::init        ();
    if ($with_dynamic) static::init_dynamic();
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

  static function insert($id, $managing_group = null, $managing_title, $in_areas = null, $display = null, $type = null, $source = null, $weight = 0, $module_id = null) {
    static::init();
    $new_part = new static($id, $managing_group, $managing_title, $in_areas, $display, $type, $source, $weight);
           static::$cache[$id] = $new_part;
           static::$cache[$id]->module_id = $module_id;
           static::$cache[$id]->origin = 'dynamic';
    return static::$cache[$id];
  }

}}