<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text extends text_simple {

  public $args = [];
  public $is_apply_translation = true;
  public $is_apply_tokens = false;

  function __construct($text = '', $args = [], $weight = 0) {
    parent::__construct($text, $weight);
    if ($args) $this->args_set($args);
  }

  function args_get() {return $this->args;}
  function args_set($args) {$this->args = $args;}

  function render() {
    $result = $this->text;
    if ($this->is_apply_translation) $result = translation::get($result, $this->args);
    if ($this->is_apply_tokens)      $result = token::replace  ($result);
    return $result;
  }

}}