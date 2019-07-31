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

  function __get  ($name)         {return       $this->values[$name];         }
  function __set  ($name, $value) {             $this->values[$name] = $value;}
  function __isset($name)         {return isset($this->values[$name]);        }

  function values_set($values) {$this->values = $values;}
  function values_get($names = []) {
    if (count($names)) {
      return array_intersect_key($this->values, core::array_kmap($names));
    } else {
      return $this->values;
    }
  }

  function values_id_get() {
    return $this->entity_get()->id_get_from_values(
           $this->values_get()
    );
  }

  function entity_get() {return entity::get($this->entity_name);}
  function entity_set_name($entity_name) {$this->entity_name = $entity_name;}

  function select() {
    event::start('on_instance_select_before', $this->entity_name, [&$this]);
    $result = $this->entity_get()->storage_get()->instance_select($this);
    event::start('on_instance_select_after',  $this->entity_name, [&$this]);
    return $result;
  }

  function insert() {
    if ($this->entity_get()->ws_created) $this->created = core::datetime_get();
    if ($this->entity_get()->ws_updated) $this->updated = core::datetime_get();
    return $this->entity_get()->storage_get()->instance_insert($this);
  }

  function update() {
    if ($this->entity_get()->ws_updated) $this->updated = core::datetime_get();
    return $this->entity_get()->storage_get()->instance_update($this);
  }

  function delete() {
    return $this->entity_get()->storage_get()->instance_delete($this);
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
    if (static::$cache == null) {
      static::$cache_orig = storage::get('files')->select('instances');
      foreach (static::$cache_orig as $c_module_id => $c_instances) {
        foreach ($c_instances as $c_row_id => $c_instance) {
          if (isset(static::$cache[$c_row_id])) console::log_insert_about_duplicate('instance', $c_row_id, $c_module_id);
          static::$cache[$c_row_id] = $c_instance;
          static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($row_id) {
    static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function get_all_by_module($name) {
    static::init();
    return static::$cache_orig[$name] ?? [];
  }

}}