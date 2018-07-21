<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class storage {

  static protected $cache;

  static function init($id) {
    storage_files::init('storages');
    foreach (storage_files::$data['storages'] as $c_module_id => $c_module_storages) {
      foreach ($c_module_storages as $c_row_id => $c_storage) {
        if ($c_storage->id === $id) {
          static::$cache[$c_storage->id] = $c_storage;
        }
      }
    }
  }

  static function cache_reset() {
    static::$cache = [];
  }

  static function get($id) {
    if (!isset(static::$cache[$id])) static::init($id);
    if (static::$cache[$id] instanceof external_cache)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id];
  }

}}