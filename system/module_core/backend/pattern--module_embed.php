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
    foreach (entity::get_all_by_module($this->id) as $c_entity) {
      if ($c_entity->install())
           message::insert(new text('Entity "%%_name" was installed.',     ['name' => $c_entity->name]));
      else message::insert(new text('Entity "%%_name" was not installed!', ['name' => $c_entity->name]), 'error');
    }
  # insert instances
    foreach (instance::get_all_by_module($this->id) as $c_row_id => $c_instance) {
      if ($c_instance->insert())
           message::insert(new text('Instance with row_id = "%%_row_id" was added.',     ['row_id' => $c_row_id]));
      else message::insert(new text('Instance with row_id = "%%_row_id" was not added!', ['row_id' => $c_row_id]), 'error');
    }
  # insert to boot
    core::boot_insert($this->id, $this->path, 'installed');
  }

  function get_dependencies_status() {
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

  function get_depended_status() {
    $result = [];
    $boot_status = core::boot_select();
    foreach (static::get_all() as $c_module) {
      $c_dependencies_sys = $c_module->dependencies->system ?? [];
      if (isset($c_dependencies_sys[$this->id])) {
        $result[$c_module->id] = (int)isset($boot_status[$c_module->id]);
      }
    }
    return $result;
  }

  function group_get_id() {
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

  static function get_settings($module_id) {
    $settings = storage::get('files')->select('settings');
    return $settings[$module_id] ?? [];
  }

  static function get($id) {
    if    (static::$cache == null) static::init();
    return static::$cache['modules'][$id];
  }

  static function get_bundle($id) {
    if    (static::$cache == null) static::init();
    return static::$cache['bundles'][$id];
  }

  static function get_all($property = null) {
    $result = [];
    if      (static::$cache == null) static::init();
    foreach (static::$cache['modules'] as $c_module) {
      $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
    }
    return $result;
  }

  static function get_embed($property = null) {
    $result = [];
    foreach (static::get_all() as $c_module) {
      if ($c_module instanceof module_embed &&
         !$c_module instanceof module) {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function get_enabled_by_default($property = null) {
    $result = [];
    foreach (static::get_all() as $c_module) {
      if ($c_module->enabled == 'yes') {
        $result[$c_module->id] = $property ? $c_module->{$property} : $c_module;
      }
    }
    return $result;
  }

  static function get_groups() {
    $groups = [];
    if      (static::$cache == null) static::init();
    foreach (static::$cache['modules'] as $c_module)
      $groups[core::sanitize_id($c_module->group)] = $c_module->group;
    return $groups;
  }

  static function get_updates($module_id, $from_number = 0) {
    $updates = [];
    foreach (storage::get('files')->select('module_updates', false, false) ?? [] as $c_module_id => $c_updates)
      if ($c_module_id == $module_id)
        foreach ($c_updates as $c_row_id => $c_update)
          if ($c_update->number >= $from_number)
            $updates[$c_row_id] = $c_update;
    return $updates;
  }

  static function get_update_last_number($module_id) {
    $settings = static::get_settings($module_id);
    return $settings->update_last_number ?? 0;
  }

  static function is_required_updates() {
    foreach (static::get_all() as $c_module) {
      $c_updates            = static::get_updates           ($c_module->id);
      $c_update_last_number = static::get_update_last_number($c_module->id);
      foreach ($c_updates as $c_update) {
        if ($c_update->number > $c_update_last_number) return true;
      }
    }
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