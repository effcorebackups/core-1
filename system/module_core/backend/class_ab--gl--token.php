<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class token {

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('tokens') as $c_module_id => $c_module_tokens) {
      foreach ($c_module_tokens as $c_row_id => $c_token) {
        static::$cache[$c_token->match] = $c_token;
      }
    }
  }

  static function replace($string) {
    if (!static::$cache) static::init();
    return preg_replace_callback('%(?<name>\\%\\%_[a-z0-9_]+)'.
                                  '(?<args>\\{[a-z0-9_,]+\\}|)%S', function($matches) {
      $name = !empty($matches['name']) ? $matches['name'] : null;
      $args = !empty($matches['args']) ? explode(',', substr($matches['args'], 1, -1)) : [];
      if ($name && isset(static::$cache[$name])) {
        switch (static::$cache[$name]->type) {
          case 'code': return call_user_func(static::$cache[$name]->handler, $name, $args);
          case 'text': return static::$cache[$name]->value;
          case 'translated_text': return translation::get(static::$cache[$name]->value);
        }
      } else {
        return '';
      }
    }, $string);
  }

}}