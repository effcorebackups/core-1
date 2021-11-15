<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class text_multiline extends text {

  public $text = [];
  public $args = [];
  public $delimiter = br;

  function __construct($text = [], $args = [], $delimiter = br, $with_translation = true, $with_tokens = false, $weight = 0) {
    if ($text) $this->text_update($text);
    if ($args) $this->args_set($args);
    $this->delimiter            = $delimiter;
    $this->is_apply_translation = $with_translation;
    $this->is_apply_tokens      = $with_tokens;
    $this->weight               = $weight;
  }

  function text_lines_count()                {return count($this->text);}
  function text_line_select($key)            {if (array_key_exists($key, $this->text)) return $this->text[$key];             else return null;}
  function text_line_append($key, $new_text) {if (array_key_exists($key, $this->text))        $this->text[$key].= $new_text; else $this->text[$key] = $new_text;}
  function text_line_update($key, $new_text) {$this->text[$key] = $new_text;}
  function text_line_delete($key)            {unset($this->text[$key]);}

  function text_select()          {return $this->text;}
  function text_update($new_text) {$this->text   = $new_text;}
  function text_append($new_line) {$this->text[] = $new_line; return count($this->text);}
  function text_delete()          {$this->text   = [];}

  function text_length($is_in_bytes = false) {
    if ($is_in_bytes === true) return    strlen($this->render());
    if ($is_in_bytes !== true) return mb_strlen($this->render());
  }

  function render() {
    $result = [];
    foreach ($this->text as $c_line) {
      $c_result = $c_line;
      if ($this->is_apply_translation) $c_result = translation::apply($c_result, $this->args);
      if ($this->is_apply_tokens)      $c_result =       token::apply($c_result);
      $result[] = $c_result;
    }
    return implode(
      html_entity_decode($this->delimiter), $result
    );
  }

}}