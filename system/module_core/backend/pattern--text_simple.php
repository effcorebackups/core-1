<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text_simple {

  public $text = '';
  public $weight = 0;

  function __construct($text = '', $weight = 0) {
    if ($text !== '') $this->text_update($text);
    if ($weight)      $this->weight = $weight;
  }

  function text_select() {return $this->text;}
  function text_update($text) {$this->text = $text;}
  function text_append($text) {$this->text.= $text;}

  function render() {
    return $this->text;
  }

}}