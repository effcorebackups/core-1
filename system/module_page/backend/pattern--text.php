<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text extends text_simple {

  public $args = [];
  public $is_apply_translation = true;
  public $is_apply_tokens = false;

  function __construct($text = '', $args = [], $with_translation = true, $with_tokens = false, $weight = 0) {
    if ($text !== '') $this->text_update($text);
    if ($args       ) $this->args_set   ($args);
    $this->is_apply_translation = $with_translation;
    $this->is_apply_tokens      = $with_tokens;
    $this->weight               = $weight;
  }

  function args_get() {return $this->args;}
  function args_set($args) {$this->args = $args;}

  function render() {
    $result = translation::apply($this->text, $this->args,
        $this->is_apply_translation ? null : 'en');
    if ($this->is_apply_tokens)
           $result = token::apply($result);
    return $result;
  }

}}