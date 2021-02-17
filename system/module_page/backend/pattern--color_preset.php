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

  static function apply($id) {
    $result = true;
    $preset = static::get($id);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',                   $preset->colors->color_page_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',                   $preset->colors->color_text_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',                   $preset->colors->color_main_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',                   $preset->colors->color_link_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',            $preset->colors->color_link_active_id,            false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_odd_id',          $preset->colors->color_table_row_odd_id,          false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_even_id',         $preset->colors->color_table_row_even_id,         false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_relation_id',               $preset->colors->color_relation_id,               false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',                   $preset->colors->color_menu_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',            $preset->colors->color_menu_active_id,            false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',              $preset->colors->color_menu_text_id,              false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',              $preset->colors->color_menu_link_id,              false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id',       $preset->colors->color_menu_link_active_id,       false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_id',                   $preset->colors->color_tabs_id,                   false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_id',              $preset->colors->color_tabs_link_id,              false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_id',       $preset->colors->color_tabs_link_active_id,       false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_no_bg_id', $preset->colors->color_tabs_link_active_no_bg_id, false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',                     $preset->colors->color_ok_id,                     false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',                $preset->colors->color_warning_id,                false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',                  $preset->colors->color_error_id,                  false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_id',               $preset->colors->color_fieldset_id,               false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_nested_id',        $preset->colors->color_fieldset_nested_id,        false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_id',                  $preset->colors->color_field_id,                  false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_text_id',             $preset->colors->color_field_text_id,             false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',                 $preset->colors->color_button_id,                 false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',          $preset->colors->color_button_active_id,          false);
    $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_text_id',            $preset->colors->color_button_text_id                  );
    return $result;
  }

  static function reset() {
    $result = true;
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_page_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_text_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_main_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_active_id',            false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_table_row_odd_id',          false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_table_row_even_id',         false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_relation_id',               false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_active_id',            false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_text_id',              false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_link_id',              false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_link_active_id',       false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_tabs_id',                   false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_tabs_link_id',              false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_tabs_link_active_id',       false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_tabs_link_active_no_bg_id', false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_ok_id',                     false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_warning_id',                false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_error_id',                  false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_fieldset_id',               false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_fieldset_nested_id',        false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_field_id',                  false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_field_text_id',             false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_id',                 false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_active_id',          false);
    $result&= storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_text_id'                  );
    return $result;
  }

}}