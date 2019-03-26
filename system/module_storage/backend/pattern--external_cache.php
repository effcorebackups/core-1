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

  function external_cache_load() {
    if (cache::is_exists($this->cache_name)) {
      return cache::select(
        $this->cache_name
      );
    } else {
      cache::message_select_show(
        cache::file_by_name_get($this->cache_name)
      );
    }
  }

}}