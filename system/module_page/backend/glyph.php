<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class glyph {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (!static::$cache) {
      foreach (storage::get('files')->select('glyphs') as $c_module_id => $c_characters) {
        foreach ($c_characters as $c_row_id => $c_character) {
          foreach ($c_character->glyphs as $c_group => $c_glyph) {
            if (isset(static::$cache[$c_glyph])) console::log_insert_about_duplicate('glyph', $c_glyph, $c_module_id);
                      static::$cache[$c_glyph] = (string)$c_character->character;
          }
        }
      }
    }
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function get_by_character($character) {
    static::init();
    $result = [];
    foreach (static::$cache as $c_glyph => $c_character) {
      if ($c_character === $character) {
        $result[$c_glyph] = $c_character;
      }
    }
    return $result;
  }

}}