<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class block_preset extends block {

  public $id;
  public $managing_group = 'Text';
  public $managing_title;
  public $in_areas;
  public $origin = 'nosql'; # nosql | dynamic

  function __construct($id = null, $managing_group = null, $managing_title = null, $in_areas = null, $display = null, $type = null, $source = null, $properties = [], $args = [], $weight = 0) {
    if ($id            ) $this->id             = $id;
    if ($managing_group) $this->managing_group = $managing_group;
    if ($managing_title) $this->managing_title = $managing_title;
    if ($in_areas      ) $this->in_areas       = $in_areas;
    if ($display       ) $this->display        = $display;
    if ($type          ) $this->type           = $type;
    if ($source        ) $this->source         = $source;
    if ($properties    ) $this->properties     = $properties;
    if ($args          ) $this->args           = $args;
    parent::__construct($weight);
  }

  function block_build() {
    $block = new block;
    foreach ($this as $c_key => $c_value)
      $block->{$c_key} =
      $this ->{$c_key};
    return $block;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $is_init_nosql   = false;
  static protected $is_init_dynamic = false;

  static function cache_cleaning() {
    static::$cache           = null;
    static::$is_init_nosql   = false;
    static::$is_init_dynamic = false;
  }

  static function init() {
    if (!static::$is_init_nosql) {
         static::$is_init_nosql = true;
      foreach (storage::get('files')->select('block_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::log_insert_about_duplicate('block_preset', $c_preset->id, $c_module_id);
                    static::$cache[$c_preset->id] = $c_preset;
                    static::$cache[$c_preset->id]->module_id = $c_module_id;
                    static::$cache[$c_preset->id]->origin = 'nosql';
        }
      }
    }
  }

  static function init_dynamic($id = null) {
    if ($id === null && !static::$is_init_dynamic) {static::$is_init_dynamic = true; event::start('on_block_presets_dynamic_build', null       );}
    if ($id !== null                             ) {                                 event::start('on_block_presets_dynamic_build', null, [$id]);}
  }

  static function select_all($id_area = null, $origin = null) {
    if ($origin ===   'nosql') {static::init();                        }
    if ($origin === 'dynamic') {static::init(); static::init_dynamic();}
    if ($origin ===      null) {static::init(); static::init_dynamic();}
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
    if (isset(static::$cache[$id]) === false) static::init_dynamic($id);
    return static::$cache[$id] ?? null;
  }

  static function insert($id, $managing_group = null, $managing_title, $in_areas = null, $display = null, $type = null, $source = null, $properties = [], $args = [], $weight = 0, $module_id = null) {
    static::init();
    $new_preset = new static($id, $managing_group, $managing_title, $in_areas, $display, $type, $source, $properties, $args, $weight);
           static::$cache[$id] = $new_preset;
           static::$cache[$id]->module_id = $module_id;
           static::$cache[$id]->origin = 'dynamic';
    return static::$cache[$id];
  }

}}