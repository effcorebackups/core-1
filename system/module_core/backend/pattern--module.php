<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class module {

  public $id;
  public $title;
  public $description;
  public $version;
  public $always_on = 'no';
  public $path;

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

  function uninstall() {
  # delete instances
    foreach (instance::all_by_module_get($this->id) as $c_instance) {
      if ($c_instance->select() &&
          $c_instance->delete())
           message::insert(translation::get('Instances of entity %%_name was deleted.',     ['name' => $c_instance->entity_name]));
      else message::insert(translation::get('Instances of entity %%_name was not deleted!', ['name' => $c_instance->entity_name]), 'error');
    }
  # delete entities
    foreach (entity::all_by_module_get($this->id) as $c_entity) {
      if ($c_entity->uninstall())
           message::insert(translation::get('Entity %%_name was uninstalled.',     ['name' => $c_entity->name]));
      else message::insert(translation::get('Entity %%_name was not uninstalled!', ['name' => $c_entity->name]), 'error');
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

  static function enabled_get() {
    return storage::get('files')->select('settings/core/modules_enabled');
  }

}}