<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class token {

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function text_decode($text) {
    return str_replace(['\\{', '\\}', '\\|'], ['{', '}', '|'], $text);
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('data')->select_array('tokens') as $c_module_id => $c_tokens) {
        foreach ($c_tokens as $c_row_id => $c_token) {
          if (isset(static::$cache[$c_row_id])) console::report_about_duplicate('tokens', $c_row_id, $c_module_id, static::$cache[$c_row_id]);
                    static::$cache[$c_row_id] = $c_token;
                    static::$cache[$c_row_id]->origin = 'nosql';
                    static::$cache[$c_row_id]->module_id = $c_module_id;
        }
      }
    }
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

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
                    $new_token->type      = $type;
                    $new_token->value     = $value;
                    $new_token->origin    = 'dynamic';
    if ($handler  ) $new_token->handler   = $handler;
    if ($module_id) $new_token->module_id = $module_id;
           static::$cache[$row_id] = $new_token;
    return static::$cache[$row_id];
  }

  static function apply($string) {
    return preg_replace_callback('%\\%\\%_'.'(?<name>[a-z0-9_]{1,64})'.
                                '(?:'.'\\{'.'(?<args>.{1,1024}?)'.'(?<!\\\\)'.'\\}|)%S', function ($c_match) {
      $c_name =       $c_match['name'];
      $c_args = isset($c_match['args']) ? preg_split('%(?<!\\\\)\\|%S',
                      $c_match['args']) : [];
      $c_info = static::select($c_name);
      if ($c_info) {
        switch ($c_info->type) {
          case 'code': return call_user_func($c_info->handler, $c_name, $c_args);
          case 'text': return $c_info->value;
        }
      } else {
        return '';
      }
    }, $string);
  }

}}