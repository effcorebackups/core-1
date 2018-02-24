<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class different_cache {

  public $cache_name;

  function __construct($cache_name = '', $properties = []) {
    if ($cache_name) $this->cache_name = $cache_name;
    foreach ($properties as $c_key => $c_value) {
      $this->{$c_key} = $c_value;
    }
  }

  function get_cache_name() {
    return $this->cache_name;
  }

  function get_different_cache() {
    return cache::select(
      $this->get_cache_name()
    );
  }

}}