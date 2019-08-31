<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class token {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      foreach (storage::get('files')->select('tokens') as $c_module_id => $c_tokens) {
        foreach ($c_tokens as $c_row_id => $c_token) {
          if (isset(static::$cache[$c_row_id])) console::log_insert_about_duplicate('token', $c_row_id, $c_module_id);
          static::$cache[$c_row_id] = $c_token;
          static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($row_id) {
    static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function replace($string) {
    return preg_replace_callback('%\\%\\%_(?<name>[a-z0-9_]+)(?:\\{(?<args>[a-z0-9_,=\'\\"\\-]+)\\}|)%S', function($c_match) {
      $c_name =       $c_match['name'];
      $c_args = isset($c_match['args']) ? explode(',', $c_match['args']) : [];
      $c_info = static::get($c_name);
      if ($c_info) {
        switch ($c_info->type) {
          case 'code'           : return call_user_func($c_info->handler, $c_name, $c_args);
          case 'text'           : return $c_info->value;
          case 'translated_text': return translation::get($c_info->value);
        }
      } else {
        return '';
      }
    }, $string);
  }

}}