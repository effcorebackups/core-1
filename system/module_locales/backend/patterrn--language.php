<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
        if (isset(static::$cache[$c_language->code])) console::log_about_duplicate_add('language', $c_language->code);
        static::$cache[$c_language->code] = $c_language;
        static::$cache[$c_language->code]->module_id = $c_module_id;
      }
    }
    foreach (storage::get('files')->select('plurals') as $c_module_id => $c_plurals_by_module) {
      foreach ($c_plurals_by_module as $c_plurals_by_language) {
        foreach ($c_plurals_by_language->data as $c_plural_name => $c_plural_info) {
          if (isset(static::$cache[$c_plurals_by_language->code]))
                    static::$cache[$c_plurals_by_language->code]->plurals[$c_plural_name] = $c_plural_info;
        }
      }
    }
  }

  static function get($code) {
    if    (static::$cache == null) static::init();
    return static::$cache[$code];
  }

  static function get_plurals($code) {
    return static::get($code)->plurals ?? [];
  }

  static function current_get() {
    return locale::settings_get()->lang_code;
  }

}}