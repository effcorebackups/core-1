<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
    $string =  static::$cache[$c_code][$string] ?? $string;
    return preg_replace_callback('%\\%\\%_(?<name>[a-z0-9_]+)(?:\\{(?<args>[a-z0-9_,=\'"-]+)\\}|)%S', function($c_match) use ($c_code, $args) {
      $c_name =       $c_match['name'];
      $c_args = isset($c_match['args']) ? explode(',', $c_match['args']) : [];
    # plurals functionality
      if ($c_name == 'plural') {
        if (isset($c_args[0]) &&
            isset($c_args[1])) {
          $p_number_name = $c_args[0];
          $p_plural_type = $c_args[1];
          $p_plurals     = language::get_plurals($c_code);
          if (isset($p_plurals[$p_plural_type]) &&
                   isset($args[$p_number_name])) {
            $p_plural_info = $p_plurals[$p_plural_type];
            $p_matches     = [];
            if (preg_match($p_plural_info->formula, (string)$args[$p_number_name], $p_matches)) {
              $replacement = array_intersect_key($p_plural_info->matches, array_filter($p_matches, 'strlen'));
              return reset($replacement);
            } else {
              return '';
            }
          }
        }
      }
    # default case
      return $args[$c_name] ?? $c_match[0];
    }, $string);
  }

}}