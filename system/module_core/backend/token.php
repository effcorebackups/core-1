<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class token {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('files')->select('tokens') ?? [] as $c_module_id => $c_tokens) {
        foreach ($c_tokens as $c_row_id => $c_token) {
          if (isset(static::$cache[$c_row_id])) console::report_about_duplicate('token', $c_row_id, $c_module_id);
                    static::$cache[$c_row_id] = $c_token;
                    static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function select($row_id) {
    static::init();
    return static::$cache[$row_id] ?? null;
  }

  static function select_all() {
    static::init();
    return static::$cache;
  }

  static function insert($row_id, $type, $value = null, $handler = null, $module_id = null) {
    static::init();
    $new_token = new \stdClass;
    if (true      ) $new_token->type      = $type;
    if (true      ) $new_token->value     = $value;
    if ($handler  ) $new_token->handler   = $handler;
    if ($module_id) $new_token->module_id = $module_id;
           static::$cache[$row_id] = $new_token;
    return static::$cache[$row_id];
  }

  static function apply($string) {
    return preg_replace_callback('%\\%\\%_'.'(?<name>[a-z0-9_]{1,64})'.
                                   '(?:\\{'.'(?<args>[^\\}\\n]{1,1024})'.'\\}|)%S', function ($c_match) {
      $c_name =       $c_match['name'];
      $c_args = isset($c_match['args']) ? explode('|', $c_match['args']) : [];
      $c_info = static::select($c_name);
      if ($c_info) {
        switch ($c_info->type) {
          case 'code'           : return call_user_func($c_info->handler, $c_name, $c_args);
          case 'text'           : return $c_info->value;
          case 'translated_text': return translation::apply($c_info->value);
        }
      } else {
        return '';
      }
    }, $string);
  }

}}