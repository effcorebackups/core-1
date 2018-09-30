<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class entity
          implements has_external_cache, has_post_parse {

  public $name;
  public $storage_id;
  public $catalog_id;
  public $ws_created;
  public $ws_updated;
  public $title;
  public $title_plural;
  public $fields = [];
  public $constraints = [];
  public $indexes = [];

  function __post_parse() {
  # add field 'created' and index for it
    if ($this->ws_created) {
      $this->fields['created'] = new \stdClass;
      $this->fields['created']->title = 'Created';
      $this->fields['created']->type = 'datetime';
      $this->fields['created']->not_null = true;
      $this->indexes['index_created'] = new \stdClass;
      $this->indexes['index_created']->type = 'index';
      $this->indexes['index_created']->fields = ['created' => 'created'];
    }
  # add field 'updated' and index for it
    if ($this->ws_updated) {
      $this->fields['updated'] = new \stdClass;
      $this->fields['updated']->title = 'Updated';
      $this->fields['updated']->type = 'datetime';
      $this->fields['updated']->not_null = true;
      $this->indexes['index_updated'] = new \stdClass;
      $this->indexes['index_updated']->type = 'index';
      $this->indexes['index_updated']->fields = ['updated' => 'updated'];
    }
  }

  function field_get($name) {
    return $this->fields[$name] ?? null;
  }

  function fields_name_get() {
    return core::array_kmap(
      array_keys($this->fields)
    );
  }

  function auto_name_get() {
    foreach ($this->fields as $name => $info) {
      if ($info->type == 'autoincrement') {
        return $name;
      }
    }
  }

  function key_primary_fields_get() {
    foreach ($this->constraints as $c_constraint)
      if ($c_constraint->type == 'primary')
        return core::array_kmap($c_constraint->fields);
    return [];
  }

  function keys_unique_fields_get() {
    $fields = [];
    foreach ($this->constraints as $c_constraint)
      if ($c_constraint->type == 'unique')
        $fields += $c_constraint->fields;
    return core::array_kmap($fields);
  }

  function install() {
    $storage = storage::get($this->storage_id);
    return $storage->entity_install($this);
  }

  function uninstall() {
    $storage = storage::get($this->storage_id);
    return $storage->entity_uninstall($this);
  }

  function instances_select($conditions = [], $order = [], $count = 0, $offset = 0) {
    $storage = storage::get($this->storage_id);
    return $storage->instances_select($this, $conditions, $order, $count, $offset);
  }

  function instances_insert() {} # @todo: make functionality
  function instances_delete() {} # @todo: make functionality

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_orig;

  static function not_external_properties_get() {
    return [
      'name'         => 'name',
      'title'        => 'title',
      'title_plural' => 'title_plural',
      'storage_id'   => 'storage_id',
      'catalog_id'   => 'catalog_id'
    ];
  }

  static function init() {
    static::$cache_orig = storage::get('files')->select('entities');
    foreach (static::$cache_orig as $c_module_id => $c_entities) {
      foreach ($c_entities as $c_row_id => $c_entity) {
        if (isset(static::$cache[$c_entity->name])) console::log_about_duplicate_add('entity', $c_entity->name);
        static::$cache[$c_entity->name] = $c_entity;
        static::$cache[$c_entity->name]->module_id = $c_module_id;
      }
    }
  }

  static function get($name, $load = true) {
    if (static::$cache == null) static::init();
    if (isset(static::$cache[$name]) == false) return;
    if (static::$cache[$name] instanceof external_cache && $load)
        static::$cache[$name] = static::$cache[$name]->external_cache_load();
    return static::$cache[$name];
  }

  static function all_get($load = true) {
    if (static::$cache == null) static::init();
    if ($load)
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item = $c_item->external_cache_load();
    return static::$cache;
  }

  static function all_by_module_get($module, $load = true) {
    if (static::$cache_orig == null) static::init();
    if ($load && isset(static::$cache_orig[$module]))
      foreach (static::$cache_orig[$module] as &$c_item)
        if ($c_item instanceof external_cache)
            $c_item = $c_item->external_cache_load();
    return static::$cache_orig[$module] ?? [];
  }

}}