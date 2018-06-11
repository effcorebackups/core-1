<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class storage {

  static protected $cache;

  static function init($storage_id) {
    storage_files::init('storages');
    foreach (storage_files::$data['storages'] as $c_module_id => $c_module_storages) {
      foreach ($c_module_storages as $c_row_id => $c_storage) {
        if ($c_storage->id === $storage_id) {
          if ($c_storage instanceof external_cache)
              $c_storage = $c_storage->external_cache_load();
          static::$cache[$c_storage->id] = $c_storage;
        }
      }
    }
  }

  static function cache_reset() {
    static::$cache = [];
  }

  static function get($storage_id) {
    if (!isset(static::$cache[$storage_id])) static::init($storage_id);
    return static::$cache[$storage_id];
  }

}}