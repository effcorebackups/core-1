<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class language {

  public $code;
  public $title;

  ######################
  ### static methods ###
  ######################

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('languages') as $c_module_id => $c_module_languages) {
      foreach ($c_module_languages as $c_row_id => $c_language) {
        static::$cache[$c_language->code] = $c_language;
      }
    }
  }

  static function get($code) {
    if   (!static::$cache) static::init();
    return static::$cache[$code];
  }

  static function get_current() {
    return locale::get_settings()->lang_code;
  }

}}