<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class entity
          implements has_external_cache, has_post_parsing {

  public $name;
  public $storage_id;
  public $catalog_id;
  public $ws_created;
  public $ws_updated;
  public $title;
  public $fields = [];
  public $constraints = [];
  public $indexes = [];

  function __post_parsing() {
  # add field "created" and index for it
    if ($this->ws_created) {
      $this->fields->created = new \stdClass;
      $this->fields->created->type = 'datetime';
      $this->fields->created->not_null = true;
      $this->indexes['idx_created'] = new \stdClass;
      $this->indexes['idx_created']->type = 'index';
      $this->indexes['idx_created']->fields = ['created' => 'created'];
    }
  # add field "updated" and index for it
    if ($this->ws_updated) {
      $this->fields->updated = new \stdClass;
      $this->fields->updated->type = 'datetime';
      $this->fields->updated->not_null = true;
      $this->indexes['idx_updated'] = new \stdClass;
      $this->indexes['idx_updated']->type = 'index';
      $this->indexes['idx_updated']->fields = ['updated' => 'updated'];
    }
  }

  function name_get()             {return $this->name;}
  function storage_id_get()       {return $this->storage_id;}
  function catalog_id_get()       {return $this->catalog_id;}
  function indexes_info_get()     {return $this->indexes;}
  function constraints_info_get() {return $this->constraints;}
  function field_info_get($name)  {return $this->fields->{$name};}
  function fields_info_get()      {return $this->fields;}
  function fields_get() {
    return core::array_kmap(
      array_keys((array)$this->fields)
    );
  }

  function auto_name_get() {
    foreach ($this->fields as $name => $info) {
      if ($info->type == 'autoincrement') {
        return $name;
      }
    }
  }

  function keys_get($primary = true, $unique = true) {
    $keys = [];
    foreach ($this->constraints as $c_cstr) {
      if (($c_cstr->type == 'primary key' && $primary) ||
          ($c_cstr->type == 'unique'      && $unique)) {
        $keys += $c_cstr->fields;
      }
    }
    return core::array_kmap($keys);
  }

  function install() {
    $storage = storage::get($this->storage_id_get());
    return $storage->entity_install($this);
  }

  function uninstall() {
    $storage = storage::get($this->storage_id_get());
    return $storage->entity_uninstall($this);
  }

  function instances_select($conditions = [], $order = [], $count = 0, $offset = 0) {
    $storage = storage::get($this->storage_id_get());
    return $storage->instances_select($this, $conditions, $order, $count, $offset);
  }

  function instances_insert() {} # todo: make functionality
  function instances_delete() {} # todo: make functionality

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_orig;

  static function not_external_properties_get() {
    return [
      'name'       => 'name',
      'storage_id' => 'storage_id',
      'catalog_id' => 'catalog_id',
      'title'      => 'title'
    ];
  }

  static function init($name = '') {
    static::$cache_orig = storage::get('files')->select('entities');
    foreach (static::$cache_orig as $c_module_id => $c_entities) {
      foreach ($c_entities as $c_row_id => $c_entity) {
        if ($name == '' || (
            $name && $name == $c_entity->name)) {
          if ($c_entity instanceof external_cache)
              $c_entity = $c_entity->external_cache_load();
          if (isset(static::$cache[$c_entity->name])) console::add_log_about_duplicate('entity', $c_entity->name);
          static::$cache[$c_entity->name] = $c_entity;
          static::$cache[$c_entity->name]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($name) {
    if (!isset(static::$cache[$name])) static::init($name);
    return     static::$cache[$name];
  }

  static function all_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function all_by_module_get($module) {
    if   (!static::$cache_orig) static::init();
    return static::$cache_orig[$module];
  }

}}