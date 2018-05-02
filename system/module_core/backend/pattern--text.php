<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text extends text_simple {

  public $args = [];

  function __construct($text = '', $args = [], $weight = 0) {
    parent::__construct($text, $weight);
    if ($args) $this->args_set($args);
  }

  function args_get() {return $this->args;}
  function args_set($args) {$this->args = $args;}

  function render() {
    return translation::get($this->text, $this->args);
  }

}}