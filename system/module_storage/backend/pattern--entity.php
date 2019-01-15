<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class entity implements has_external_cache, should_clear_cache_after_on_install, has_postparse {

  public $name;
  public $storage_name = 'sql';
  public $catalog_name;
  public $ws_weight;
  public $ws_created;
  public $ws_updated;
  public $title;
  public $title_plural;
  public $fields = [];
  public $constraints = [];
  public $indexes = [];

  function _postparse() {
  # insert field 'weight' and index for it
    if ($this->ws_weight) {
      $this->fields['weight'] = new \stdClass;
      $this->fields['weight']->title = 'Weight';
      $this->fields['weight']->type = 'integer';
      $this->fields['weight']->not_null = true;
      $this->fields['weight']->default = 0;
      $this->indexes['index_weight'] = new \stdClass;
      $this->indexes['index_weight']->type = 'index';
      $this->indexes['index_weight']->fields = ['weight' => 'weight'];
    }
  # insert field 'created' and index for it
    if ($this->ws_created) {
      $this->fields['created'] = new \stdClass;
      $this->fields['created']->title = 'Created';
      $this->fields['created']->type = 'datetime';
      $this->fields['created']->not_null = true;
      $this->indexes['index_created'] = new \stdClass;
      $this->indexes['index_created']->type = 'index';
      $this->indexes['index_created']->fields = ['created' => 'created'];
    }
  # insert field 'updated' and index for it
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

  function real_id_get() {
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'primary') return $c_constraint->fields;
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'unique' ) return $c_constraint->fields;
    foreach ($this->indexes     as $c_index     ) if ($c_index->type == 'unique index') return $c_index->fields;
    return [];
  }

  function real_id_from_values_get($values) {
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'primary') {$slice = array_intersect_key($values, $c_constraint->fields); if (count($c_constraint->fields) == count($slice)) return $slice;}
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'unique' ) {$slice = array_intersect_key($values, $c_constraint->fields); if (count($c_constraint->fields) == count($slice)) return $slice;}
    foreach ($this->indexes     as $c_index     ) if ($c_index->type == 'unique index') {$slice = array_intersect_key($values, $c_index->fields);      if (count($c_index->fields)      == count($slice)) return $slice;}
    return [];
  }

  function install() {
    $storage = storage::get($this->storage_name);
    return $storage->entity_install($this);
  }

  function uninstall() {
    $storage = storage::get($this->storage_name);
    return $storage->entity_uninstall($this);
  }

  function instances_select($conditions = [], $order = [], $quantity = 0, $offset = 0) {
    $storage = storage::get($this->storage_name);
    return $storage->instances_select($this, $conditions, $order, $quantity, $offset);
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
      'storage_name' => 'storage_name',
      'catalog_name' => 'catalog_name'
    ];
  }

  static function cache_cleaning() {
    static::$cache      = null;
    static::$cache_orig = null;
  }

  static function init() {
    static::$cache_orig = storage::get('files')->select('entities');
    foreach (static::$cache_orig as $c_module_id => $c_entities) {
      foreach ($c_entities as $c_row_id => $c_entity) {
        if (isset(static::$cache[$c_entity->name])) console::log_about_duplicate_insert('entity', $c_entity->name, $c_module_id);
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