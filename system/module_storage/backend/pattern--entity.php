<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class entity implements has_external_cache, should_clear_cache_after_on_install, has_postparse {

  public $name;
  public $storage_name = 'sql';
  public $catalog_name;
  public $fields                = [];
  public $constraints           = [];
  public $indexes               = [];
  public $has_parallel_checking = false;
  public $ws_is_embed           = false;
  public $ws_weight             = false;
  public $ws_created            = false;
  public $ws_updated            = false;
  public $ws_module_id          = false;
  public $ws_access             = false;
  public $ws_data               = false;

  public $title;
  public $title_plural;
  public $managing_is_enabled = false;
  public $managing_group = 'Other';
  public $decorator_view_type_single = 'ul';
  public $decorator_view_type_multiple = 'table-adaptive';
  public $managing_selection_params = [];

  function _postparse() {
  # insert field 'is_embed'
    if ($this->ws_is_embed) {
      $this->fields['is_embed'] = new \stdClass;
      $this->fields['is_embed']->title = 'Is embed';
      $this->fields['is_embed']->type = 'boolean';
      $this->fields['is_embed']->not_null = true;
      $this->fields['is_embed']->default = 0;
      $this->fields['is_embed']->managing_is_on_select = true;
      $this->fields['is_embed']->managing_form_class = '\\effcore\\field_switcher';
      $this->fields['is_embed']->managing_properties['weight'] = 95;
      $this->fields['is_embed']->managing_form_element_attributes['disabled'] = true;
      $this->fields['is_embed']->managing_selection_params['is_apply_translation'] = true;
    }
  # insert field 'weight' and index for it
    if ($this->ws_weight) {
      $this->fields['weight'] = new \stdClass;
      $this->fields['weight']->title = 'Weight';
      $this->fields['weight']->type = 'integer';
      $this->fields['weight']->not_null = true;
      $this->fields['weight']->default = 0;
      $this->fields['weight']->managing_is_on_select_multiple = true;
      $this->fields['weight']->managing_is_on_select = true;
      $this->fields['weight']->managing_is_on_insert = true;
      $this->fields['weight']->managing_is_on_update = true;
      $this->fields['weight']->managing_form_class = '\\effcore\\field_number';
      $this->fields['weight']->managing_form_element_attributes['min'] = -1000;
      $this->fields['weight']->managing_form_element_attributes['max'] = +1000;
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
  # insert field 'module_id' and index for it
    if ($this->ws_module_id) {
      $this->fields['module_id'] = new \stdClass;
      $this->fields['module_id']->title = 'Module ID';
      $this->fields['module_id']->type = 'varchar';
      $this->fields['module_id']->size = 64;
      $this->fields['module_id']->collate = 'nocase';
      $this->indexes['index_module_id'] = new \stdClass;
      $this->indexes['index_module_id']->type = 'index';
      $this->indexes['index_module_id']->fields = ['module_id' => 'module_id'];
    }
  # insert field 'access'
    if ($this->ws_access) {
      $this->fields['access'] = new \stdClass;
      $this->fields['access']->title = 'Access';
      $this->fields['access']->type = 'blob';
      $this->fields['access']->filter_select = 'unserialize';
      $this->fields['access']->filter_insert = '\\effcore\\core::data_serialize';
      $this->fields['access']->filter_update = '\\effcore\\core::data_serialize';
    }
  # insert field 'data'
    if ($this->ws_data) {
      $this->fields['data'] = new \stdClass;
      $this->fields['data']->title = 'Data';
      $this->fields['data']->type = 'blob';
      $this->fields['data']->filter_select = 'unserialize';
      $this->fields['data']->filter_insert = '\\effcore\\core::data_serialize';
      $this->fields['data']->filter_update = '\\effcore\\core::data_serialize';
    }
  }

  function storage_get() {
    return storage::get($this->storage_name);
  }

  function field_get($name) {
    return $this->fields[$name] ?? null;
  }

  function fields_get_name() {
    return core::array_kmap(
      array_keys($this->fields)
    );
  }

  function fields_get_title() {
    $result = [];
    foreach ($this->fields as $name => $info)
      $result[$name] = $info->title ?? '';
    return $result;
  }

  function auto_name_get() {
    foreach ($this->fields as $name => $info) {
      if ($info->type == 'autoincrement') {
        return $name;
      }
    }
  }

  function id_get_real() {
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'primary') return $c_constraint->fields;
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'unique' ) return $c_constraint->fields;
    foreach ($this->indexes     as $c_index     ) if ($c_index->type == 'unique index') return $c_index     ->fields;
    return [];
  }

  function id_get_from_values($values) {
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'primary') {$slice = []; foreach ($c_constraint->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_constraint->fields) == count($slice)) return $slice;}
    foreach ($this->constraints as $c_constraint) if ($c_constraint->type == 'unique' ) {$slice = []; foreach ($c_constraint->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_constraint->fields) == count($slice)) return $slice;}
    foreach ($this->indexes     as $c_index     ) if ($c_index->type == 'unique index') {$slice = []; foreach ($c_index     ->fields as $c_id) if (isset($values[$c_id])) $slice[$c_id] = $values[$c_id]; if (count($c_index     ->fields) == count($slice)) return $slice;}
    return [];
  }

  function group_managing_get_id() {
    return core::sanitize_id($this->managing_group);
  }

  function install() {
    return $this->storage_get()->entity_install($this);
  }

  function uninstall() {
    return $this->storage_get()->entity_uninstall($this);
  }

  function instances_select_count($params = []) {
    return $this->storage_get()->instances_select_count($this, $params);
  }

  function instances_select($params = [], $idkey = null) {
    return $this->storage_get()->instances_select($this, $params, $idkey);
  }

  function instances_delete($params = []) {
    return $this->storage_get()->instances_delete($this, $params);
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $cache_orig;

  static function not_external_properties_get() {
    return [
      'name'           => 'name',
      'storage_name'   => 'storage_name',
      'catalog_name'   => 'catalog_name',
      'title'          => 'title',
      'title_plural'   => 'title_plural',
      'managing_group' => 'managing_group'
    ];
  }

  static function cache_cleaning() {
    static::$cache      = null;
    static::$cache_orig = null;
  }

  static function init() {
    if (static::$cache == null) {
      static::$cache_orig = storage::get('files')->select('entities');
      foreach (static::$cache_orig as $c_module_id => $c_entities) {
        foreach ($c_entities as $c_row_id => $c_entity) {
          if (isset(static::$cache[$c_entity->name])) console::log_insert_about_duplicate('entity', $c_entity->name, $c_module_id);
          static::$cache[$c_entity->name] = $c_entity;
          static::$cache[$c_entity->name]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($name, $load = true) {
    static::init();
    if (isset(static::$cache[$name]) == false) return;
    if (static::$cache[$name] instanceof external_cache && $load)
        static::$cache[$name] =
        static::$cache[$name]->external_cache_load();
    return static::$cache[$name];
  }

  static function get_all($load = true) {
    static::init();
    if ($load)
      foreach (static::$cache as $c_name => $c_item)
           if (static::$cache[$c_name] instanceof external_cache)
               static::$cache[$c_name] =
               static::$cache[$c_name]->external_cache_load();
    return static::$cache;
  }

  static function get_all_by_module($module, $load = true) {
    static::init();
    if ($load && isset(static::$cache_orig[$module]))
      foreach (static::$cache_orig[$module] as $c_name => $c_item)
           if (static::$cache_orig[$module][$c_name] instanceof external_cache)
               static::$cache_orig[$module][$c_name] =
               static::$cache_orig[$module][$c_name]->external_cache_load();
    return static::$cache_orig[$module] ?? [];
  }

  static function groups_managing_get() {
    static::init();
    $groups = [];
    foreach (static::$cache as $c_item)
      $groups[core::sanitize_id($c_item->managing_group)] = $c_item->managing_group;
    return $groups;
  }

}}