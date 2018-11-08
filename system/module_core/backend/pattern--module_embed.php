<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module_embed {

  public $id;
  public $title;
  public $description;
  public $version;
  public $path;
  public $enabled = 'yes';

  function install() {
  # insert entities
    foreach (entity::all_by_module_get($this->id) as $c_entity) {
      if ($c_entity->install())
           message::insert(translation::get('Entity %%_name was installed.',     ['name' => $c_entity->name]));
      else message::insert(translation::get('Entity %%_name was not installed!', ['name' => $c_entity->name]), 'error');
    }
  # insert instances
    foreach (instance::all_by_module_get($this->id) as $c_instance) {
      if ($c_instance->insert())
           message::insert(translation::get('Instances of entity %%_name was added.',     ['name' => $c_instance->entity_name]));
      else message::insert(translation::get('Instances of entity %%_name was not added!', ['name' => $c_instance->entity_name]), 'error');
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    static::$cache = storage::get('files')->select('module');
  }

  static function get($id) {
    if    (static::$cache == null) static::init();
    return static::$cache[$id];
  }

  static function all_get() {
    if    (static::$cache == null) static::init();
    return static::$cache;
  }

  static function embed_get() {
    $result = [];
    foreach (static::all_get() as $c_module) {
      if ($c_module instanceof module_embed &&
         !$c_module instanceof module) {
        $result[$c_module->id] = $c_module;
      }
    }
    return $result;
  }

  static function enabled_by_default_get() {
    $result = [];
    foreach (static::all_get() as $c_module) {
      if ($c_module->enabled == 'yes') {
        $result[$c_module->id] = $c_module;
      }
    }
    return $result;
  }

  static function enabled_by_boot_get() {
    $boot = data::select('boot') ?: new \stdClass;
    return array_intersect_key(static::all_get(), $boot->modules_enabled);
  }

  static function enabled_by_boot_set($enabled) {
    $boot = data::select('boot') ?: new \stdClass;
    $boot->modules_enabled = $enabled;
    data::update('boot', $boot, '', ['build' => core::datetime_get()]);
  }

  static function installed_by_boot_get() {
    $boot = data::select('boot') ?: new \stdClass;
    return array_intersect_key(static::all_get(), $boot->modules_installed);
  }

  static function installed_by_boot_set($installed) {
    $boot = data::select('boot') ?: new \stdClass;
    $boot->modules_installed = $installed;
    data::update('boot', $boot, '', ['build' => core::datetime_get()]);
  }

}}