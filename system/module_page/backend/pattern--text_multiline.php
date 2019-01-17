<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text_multiline extends text {

  public $text = [];
  public $args = [];
  public $delimiter = br;

  function __construct($text = [], $args = [], $delimiter = br, $weight = 0) {
    if ($text)      $this->text_update($text);
    if ($args)      $this->args_set($args);
    if ($delimiter) $this->delimiter = $delimiter;
    if ($weight)    $this->weight    = $weight;
  }

  function text_line_select($line) {return $this->text[$line];}
  function text_line_update($new_text, $line) {$this->text[$line] = $new_text;}
  function text_line_append($new_text, $line) {$this->text[$line].= $new_text;}
  function text_update($new_text) {$this->text   = $new_text;}
  function text_append($new_text) {$this->text[] = $new_text; return count($this->text);}

  function render() {
    $result = [];
    foreach ($this->text as $c_line) {
      $c_result = $c_line;
      if ($this->is_apply_translation) $c_result = translation::get($c_result, $this->args);
      if ($this->is_apply_tokens)      $c_result = token::replace  ($c_result);
      $result[] = $c_result;
    }
    return implode($this->delimiter, $result);
  }

}}