<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class translation
          implements has_external_cache {

  public $code;
  public $data;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return ['code' => 'code'];
  }

  static function init($code) {
    $translations = storage::get('files')->select('translations');
    foreach ($translations as $c_module_id => $c_translations) {
      foreach ($c_translations as $c_row_id => $c_translation) {
        if ($c_translation->code == $code) {
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
    $c_code = $code ?: language::current_get();
    if (!isset(static::$cache[$c_code])) static::init($c_code);
    $string = isset(static::$cache[$c_code][$string]) ?
                    static::$cache[$c_code][$string] : $string;
    return preg_replace_callback('%\\%\\%_(?<name>[a-z0-9_]+)(?:\\{(?<args>[a-z0-9_,=\'"]+)\\}|)%S', function($c_match) use ($args) {
      $c_name =       $c_match['name'];
      $c_args = isset($c_match['args']) ? explode(',', $c_match['args']) : [];
      return isset($args[$c_name]) ?
                   $args[$c_name] : '';
    }, $string);
  }

}}