<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text_multiline extends text {

  public $text = [];
  public $args = [];
  public $delimiter = br;

  function __construct($text = [], $args = [], $weight = 0) {
    if ($text)   $this->text_update($text);
    if ($args)   $this->args_set($args);
    if ($weight) $this->weight = $weight;
  }

  function text_line_select($line) {return $this->text[$line];}
  function text_line_update($new_text, $line) {$this->text[$line] = $new_text;}
  function text_line_append($new_text, $line) {$this->text[$line].= $new_text;}
  function text_update($new_text) {$this->text = $new_text;}
  function text_append($new_text) {$line = count($this->text); $this->text[$line] = $new_text; return $line;}

  function render() {
    $return = [];
    foreach ($this->text as $c_line) {
      $return[] = translation::get($c_line, $this->args);
    }
    return implode($this->delimiter, $return);
  }

}}