<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class instance {

  public $entity_name;
  public $values;

  function __construct($entity_name = '', $values = []) {
    $this->set_entity_name($entity_name);
    $this->set_values($values);
  }

  function __get($name)         {return $this->values[$name];}
  function __set($name, $value) {$this->values[$name] = $value;}

  function set_values($values) {$this->values = $values;}
  function get_values($names = []) {
    if (count($names)) {
      return array_intersect_key($this->values, factory::array_values_map_to_keys($names));
    } else {
      return $this->values;
    }
  }

  function get_entity() {return entity::get($this->entity_name);}
  function set_entity_name($entity_name) {$this->entity_name = $entity_name;}

  function select($custom_ids = []) {
    $storage = storage::get($this->get_entity()->get_storage_id());
    return $storage->select_instance($this, $custom_ids);
  }

  function insert() {
    $storage = storage::get($this->get_entity()->get_storage_id());
    return $storage->insert_instance($this);
  }

  function update() {
    $storage = storage::get($this->get_entity()->get_storage_id());
    return $storage->update_instance($this);
  }

  function delete() {
    $storage = storage::get($this->get_entity()->get_storage_id());
    return $storage->delete_instance($this);
  }

  function render() {
  }

  ######################
  ### static methods ###
  ######################

  static protected $cache;
  static protected $cache_orig;

  static function init() {
    static::$cache_orig = storage::get('files')->select('instances');
    foreach (static::$cache_orig as $c_module_id => $c_module_instances) {
      foreach ($c_module_instances as $c_row_id => $c_instance) {
        static::$cache[$c_row_id] = $c_instance;
      }
    }
  }

  static function get($row_id) {
    if   (!static::$cache) static::init();
    return static::$cache[$row_id];
  }

  static function get_by_module($name) {
    if   (!static::$cache_orig) static::init();
    return static::$cache_orig[$name];
  }

}}