<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text_raw {

  public $text = '';
  public $args = [];
  public $weight = 0;

  function __construct($text = '', $args = [], $weight = 0) {
    if ($text !== '') $this->text_update($text);
    if ($args)        $this->args_set($args);
    if ($weight)      $this->weight = $weight;
  }

  function text_select() {return $this->text;}
  function text_update($text) {$this->text = $text;}
  function text_append($text) {$this->text.= $text;}

  function args_get() {return $this->args;}
  function args_set($args) {$this->args = $args;}

  function render() {
    $return = $this->text;
    foreach ($this->args as $c_key => $c_value) {
      $return = str_replace('%%_'.$c_key, $c_value, $return);
    }
    return $return;
  }

}}