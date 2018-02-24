<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class translation
          implements \effectivecore\has_different_cache {

  public $code;
  public $data;

  ######################
  ### static methods ###
  ######################

  static protected $cache;

  static function get_non_different_properties() {
    return ['code' => 'code'];
  }

  static function init($code) {
    $translations = storage::get('files')->select('translations');
    foreach ($translations as $c_module_id => $c_module_translations) {
      foreach ($c_module_translations as $c_row_id => $c_translation) {
        if ($c_translation->code === $code) {
          if ($c_translation instanceof different_cache)
              $c_translation = $c_translation->get_different_cache();
          if (!isset(static::$cache[$c_translation->code]))
                     static::$cache[$c_translation->code] = [];
          static::$cache[$c_translation->code] += $c_translation->data;
        }
      }
    }
  }

  static function get($string, $args = [], $code = '') {
    $c_code = $code ?: language::get_current();
    if (!isset(static::$cache[$c_code])) static::init($c_code);
    $string = isset(static::$cache[$c_code][$string]) ?
                    static::$cache[$c_code][$string] : $string;
    foreach ($args as $c_key => $c_value) {
      $string = str_replace('%%_'.$c_key, $c_value, $string);
    }
    return $string;
  }

}}