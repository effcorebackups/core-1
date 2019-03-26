<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class external_cache {

  public $cache_name;

  function __construct($cache_name = '', $properties = []) {
    if ($cache_name) $this->cache_name = $cache_name;
    foreach ($properties as $c_key => $c_value) {
      $this->{$c_key} = $c_value;
    }
  }

  function external_cache_load($with_restore = true) {
    if (!cache::is_exists($this->cache_name) && $with_restore) {
      storage_nosql_files::cache_update();
    }
    return cache::select(
      $this->cache_name
    );
  }

}}