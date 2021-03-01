<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class color_preset {

  public $id;
  public $title;
  public $colors;

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('files')->select('colors_presets') as $c_module_id => $c_presets) {
        foreach ($c_presets as $c_row_id => $c_preset) {
          if (isset(static::$cache[$c_preset->id])) console::report_about_duplicate('colors_presets', $c_preset->id, $c_module_id);
          static::$cache[$c_preset->id] = $c_preset;
          static::$cache[$c_preset->id]->module_id = $c_module_id;
        }
      }
    }
  }

  static function get($id) {
    static::init();
    return static::$cache[$id] ?? null;
  }

  static function get_all() {
    static::init();
    return static::$cache;
  }

  static function is_all_colors_available() {
    $result = true;
    $colors = color::get_all();
    $settings = module::settings_get('page');
    foreach ($settings as $c_color_type => $c_color_id)
      if (substr($c_color_type, 0, 7) === 'color__')
        $result&= !empty($colors[$c_color_id]);
    return $result;
  }

  static function apply($id, $selected = null, $reset = false) {
    $preset = static::get($id);
    if ($preset) {
      $result = true;
      $storage = storage::get('files');
      foreach ($preset->colors as $c_color_type => $c_color_id)
        if (is_null($selected) || (is_array($selected) && isset($selected[$c_color_type])))
          $result&= $storage->changes_insert('page', 'update', 'settings/page/'.$c_color_type, $c_color_id, false);
      if ($reset) storage_nosql_files::cache_update();
      return $result;
    }
  }

  static function apply_with_custom_ids($selected = [], $reset = false) {
    $result = true;
    $storage = storage::get('files');
    foreach ($selected as $c_color_type => $c_color_id)
      $result&= $storage->changes_insert('page', 'update', 'settings/page/'.$c_color_type, $c_color_id, false);
    if ($reset) storage_nosql_files::cache_update();
    return $result;
  }

  static function reset($reset = true) {
    $result = true;
    $storage = storage::get('files');
    $settings = module::settings_get('page');
    foreach ($settings as $c_color_type => $c_color_id)
      if (substr($c_color_type, 0, 7) === 'color__')
        $result&= $storage->changes_delete('page', 'update', 'settings/page/'.$c_color_type, false);
    if ($reset) storage_nosql_files::cache_update();
    return $result;
  }

}}