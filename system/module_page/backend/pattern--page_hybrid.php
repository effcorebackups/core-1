<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page_hybrid extends page {

  const is_loading_was_not            = 0b00;
  const is_loading_was_not_successful = 0b01;
  const is_loading_was_____successful = 0b10;

  public $origin = 'hybrid';
  public $is_loaded = self::is_loading_was_not;

  function load_from___sql_storage() {
    if (!$this->is_loaded) {
      $instance = (new instance('page', ['id' => $this->id]))->select();
      if ($instance) {
        foreach ($instance->values_get() as $c_key => $c_value)
             $this->                       {$c_key} = $c_value;
             $this->is_loaded = static::is_loading_was_____successful;
      } else $this->is_loaded = static::is_loading_was_not_successful;
    }
    return $this;
  }

}}