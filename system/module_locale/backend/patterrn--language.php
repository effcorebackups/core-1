<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class language {

  public $code;
  public $title_en;
  public $title_native;
  public $license_path;

  function formats_get() {
    $settings = module::settings_get('locale');
    return $settings->formats[$this->code] ??
                              $this->default_formats;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;
  static protected $current;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('data')->select_array('languages') as $c_module_id => $c_languages) {
        foreach ($c_languages as $c_row_id => $c_language) {
          if (isset(static::$cache[$c_language->code])) console::report_about_duplicate('languages', $c_language->code, $c_module_id, static::$cache[$c_language->code]);
                    static::$cache[$c_language->code] = $c_language;
                    static::$cache[$c_language->code]->module_id = $c_module_id;
        }
      }
      foreach (storage::get('data')->select_array('plurals') as $c_module_id => $c_plurals_by_module) {
        foreach ($c_plurals_by_module as $c_plurals_by_language) {
          foreach ($c_plurals_by_language->data as $c_plural_name => $c_plural_info) {
            if (isset(static::$cache[$c_plurals_by_language->code]))
                      static::$cache[$c_plurals_by_language->code]->plurals[$c_plural_name] = $c_plural_info;
          }
        }
      }
    }
  }

  static function get($code) {
    static::init();
    return static::$cache[$code] ?? null;
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function plurals_get($code) {
    return static::get($code)->plurals ?? [];
  }

  static function code_get_current() {
    if   (!static::$current)
           static::$current = module::settings_get('locale')->lang_code;
    return static::$current;
  }

  static function code_set_current($code) {
    static::$current = $code;
  }

}}