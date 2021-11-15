<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
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

  function load_from_nosql_storage($with_restore = true) {
    if (!cache::is_exists($this->cache_name) && $with_restore)
      storage_nosql_data::cache_update();
    $result = cache::select($this->cache_name);
    if ($result && !empty($this->module_id)) $result->module_id = $this->module_id;
    if ($result && !empty($this->origin   )) $result->origin    = $this->origin;
    return $result;
  }

}}