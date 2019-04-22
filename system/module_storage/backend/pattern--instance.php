<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class instance implements should_clear_cache_after_on_install {

  public $entity_name;
  public $values;
  public $module_id;
  public $_id_fields_original;

  function __construct($entity_name = '', $values = []) {
    $this->entity_set_name($entity_name);
    $this->values_set($values);
  }

  function __get  ($name)         {return       $this->values[$name];}
  function __set  ($name, $value) {             $this->values[$name] = $value;}
  function __isset($name)         {return isset($this->values[$name]);}

  function values_set($values) {$this->values = $values;}
  function values_get($names = []) {
    if (count($names)) {
      return array_intersect_key($this->values, core::array_kmap($names));
    } else {
      return $this->values;
    }
  }

  function entity_get() {return entity::get($this->entity_name);}
  function entity_set_name($entity_name) {$this->entity_name = $entity_name;}

  function select() {
    $storage = storage::get($this->entity_get()->storage_name);
    return $storage->instance_select($this);
  }

  function insert() {
    $storage = storage::get($this->entity_get()->storage_name);
    if ($this->entity_get()->ws_created) $this->created = core::datetime_get();
    if ($this->entity_get()->ws_updated) $this->updated = core::datetime_get();
    return $storage->instance_insert($this);
  }

  function update() {
    $storage = storage::get($this->entity_get()->storage_name);
    if ($this->entity_get()->ws_updated) $this->updated = core::datetime_get();
    return $storage->instance_update($this);
  }

  function delete() {
    $storage = storage::get($this->entity_get()->storage_name);
    return $storage->instance_delete($this);
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_orig;

  static function cache_cleaning() {
    static::$cache      = null;
    static::$cache_orig = null;
  }

  static function init() {
    static::$cache_orig = storage::get('files')->select('instances');
    foreach (static::$cache_orig as $c_module_id => $c_instances) {
      foreach ($c_instances as $c_row_id => $c_instance) {
        if (isset(static::$cache[$c_row_id])) console::log_insert_about_duplicate('instance', $c_row_id, $c_module_id);
        static::$cache[$c_row_id] = $c_instance;
        static::$cache[$c_row_id]->module_id = $c_module_id;
      }
    }
  }

  static function get($row_id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function get_all_by_module($name) {
    if    (static::$cache_orig == null) static::init();
    return static::$cache_orig[$name] ?? [];
  }

}}