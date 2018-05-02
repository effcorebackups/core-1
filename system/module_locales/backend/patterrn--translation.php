<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class translation
          implements external {

  public $code;
  public $data;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function get_not_external_properties() {
    return ['code' => 'code'];
  }

  static function init($code) {
    $translations = storage::get('files')->select('translations');
    foreach ($translations as $c_module_id => $c_translations) {
      foreach ($c_translations as $c_row_id => $c_translation) {
        if ($c_translation->code === $code) {
          if ($c_translation instanceof external_cache)
              $c_translation = $c_translation->external_cache_load();
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