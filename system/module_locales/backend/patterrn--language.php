<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class language {

  public $code;
  public $title;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    foreach (storage::get('files')->select('languages') as $c_module_id => $c_languages) {
      foreach ($c_languages as $c_row_id => $c_language) {
        if (isset(static::$cache[$c_language->code])) console::add_log_about_duplicate('language', $c_language->code);
        static::$cache[$c_language->code] = $c_language;
        static::$cache[$c_language->code]->module_id = $c_module_id;
      }
    }
  }

  static function get($code) {
    if   (!static::$cache) static::init();
    return static::$cache[$code];
  }

  static function current_get() {
    return locale::get_settings()->lang_code;
  }

}}