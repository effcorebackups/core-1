<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module_embed {

  public $id;
  public $id_bundle;
  public $title;
  public $group = 'System';
  public $description;
  public $version;
  public $copyright;
  public $path;
  public $dependencies;
  public $enabled = 'yes';

  function enable() {
    core::boot_insert($this->id, $this->path, 'enabled');
  }

  function install() {
  # insert entities
    foreach (entity::all_by_module_get($this->id) as $c_entity) {
      if ($c_entity->install())
           message::insert(new text('Entity %%_name was installed.',     ['name' => $c_entity->name]));
      else message::insert(new text('Entity %%_name was not installed!', ['name' => $c_entity->name]), 'error');
    }
  # insert instances
    foreach (instance::all_by_module_get($this->id) as $c_instance) {
      if ($c_instance->insert())
           message::insert(new text('Instances of entity %%_name was added.',     ['name' => $c_instance->entity_name]));
      else message::insert(new text('Instances of entity %%_name was not added!', ['name' => $c_instance->entity_name]), 'error');
    }
  # insert to boot
    core::boot_insert($this->id, $this->path, 'installed');
  }

  function dependencies_status_get() {
    $dependencies_php = $this->dependencies->php    ?? [];
    $dependencies_sys = $this->dependencies->system ?? [];
    $boot_status = core::boot_select();
    foreach ($dependencies_php as $c_id => $null) $dependencies_php[$c_id] = (int)extension_loaded($dependencies_php[$c_id]);
    foreach ($dependencies_sys as $c_id => $null) $dependencies_sys[$c_id] = (int)isset($boot_status[$c_id]);
    return (object)[
      'php' => $dependencies_php,
      'sys' => $dependencies_sys
    ];
  }

  function depended_status_get() {
    $result = [];
    $boot_status = core::boot_select();
    foreach (static::all_get() as $c_module) {
      $c_dependencies_sys = $c_module->dependencies->system ?? [];
      if (isset($c_dependencies_sys[$this->id])) {
        $result[$c_module->id] = (int)isset($boot_status[$c_module->id]);
      }
    }
    return $result;
  }

  function group_id_get() {
    return core::sanitize_id($this->group);
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    static::$cache['modules'] = storage::get('files')->select('module');
    static::$cache['bundles'] = storage::get('files')->select('bundle');
  }

  static function get($id) {
    if    (static::$cache == null) static::init();
    return static::$cache['modules'][$id];
  }

  static function get_bundle($id) {
    if    (static::$cache == null) static::init();
    return static::$cache['bundles'][$id];
  }

  static function all_get($property = null) {
    $result = [];
    if      (static::$cache == null) static::init();
    foreach (static::$cache['modules'] as $c_module) {
      $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
    }
    return $result;
  }

  static function embed_get($property = null) {
    $result = [];
    foreach (static::all_get() as $c_module) {
      if ($c_module instanceof module_embed &&
         !$c_module instanceof module) {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function enabled_by_default_get($property = null) {
    $result = [];
    foreach (static::all_get() as $c_module) {
      if ($c_module->enabled == 'yes') {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function groups_get() {
    $groups = [];
    if      (static::$cache == null) static::init();
    foreach (static::$cache['modules'] as $c_module)
      $groups[core::sanitize_id($c_module->group)] = $c_module->group;
    return $groups;
  }

  static function updates_get($module_id, $from_number = 0) {
    $updates = [];
    foreach (storage::get('files')->select('module_updates') ?? [] as $c_module_id => $c_updates)
      if ($c_module_id == $module_id)
        foreach ($c_updates as $c_row_id => $c_update)
          if ($c_update->number >= $from_number)
            $updates[$c_row_id] = $c_update;
    return $updates;
  }

  static function update_last_number_get($module_id) {
    $settings = static::settings_get($module_id);
    return $settings->update_last_number ?? 0;
  }

  static function updates_is_required() {
    foreach (static::all_get() as $c_module) {
      $c_updates            = static::updates_get           ($c_module->id);
      $c_update_last_number = static::update_last_number_get($c_module->id);
      foreach ($c_updates as $c_update) {
        if ($c_update->number > $c_update_last_number) return true;
      }
    }
  }

  static function settings_get($module_id) {
    $settings = storage::get('files')->select('settings');
    return $settings[$module_id] ?? [];
  }

  static function is_enabled($module_id) {
    $enabled = core::boot_select('enabled');
    return isset($enabled[$module_id]);
  }

  static function is_installed($module_id) {
    $installed = core::boot_select('installed');
    return isset($installed[$module_id]);
  }

}}