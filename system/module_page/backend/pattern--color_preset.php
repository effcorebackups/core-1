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
    $result&= !empty($colors[$settings->color__page_id                  ]);
    $result&= !empty($colors[$settings->color__text_id                  ]);
    $result&= !empty($colors[$settings->color__main_id                  ]);
    $result&= !empty($colors[$settings->color__link_id                  ]);
    $result&= !empty($colors[$settings->color__link_active_id           ]);
    $result&= !empty($colors[$settings->color__table_row_odd_id         ]);
    $result&= !empty($colors[$settings->color__table_row_even_id        ]);
    $result&= !empty($colors[$settings->color__relation_id              ]);
    $result&= !empty($colors[$settings->color__menu_id                  ]);
    $result&= !empty($colors[$settings->color__menu_active_id           ]);
    $result&= !empty($colors[$settings->color__menu_text_id             ]);
    $result&= !empty($colors[$settings->color__menu_link_id             ]);
    $result&= !empty($colors[$settings->color__menu_link_active_id      ]);
    $result&= !empty($colors[$settings->color__tabs_id                  ]);
    $result&= !empty($colors[$settings->color__tabs_link_id             ]);
    $result&= !empty($colors[$settings->color__tabs_link_active_id      ]);
    $result&= !empty($colors[$settings->color__tabs_link_active_no_bg_id]);
    $result&= !empty($colors[$settings->color__ok_id                    ]);
    $result&= !empty($colors[$settings->color__warning_id               ]);
    $result&= !empty($colors[$settings->color__error_id                 ]);
    $result&= !empty($colors[$settings->color__fieldset_id              ]);
    $result&= !empty($colors[$settings->color__fieldset_nested_id       ]);
    $result&= !empty($colors[$settings->color__field_id                 ]);
    $result&= !empty($colors[$settings->color__field_text_id            ]);
    $result&= !empty($colors[$settings->color__button_id                ]);
    $result&= !empty($colors[$settings->color__button_active_id         ]);
    $result&= !empty($colors[$settings->color__button_text_id           ]);
    return $result;
  }

  static function apply($id, $selected = null, $reset = false) {
    $preset = static::get($id);
    if ($preset) {
      $result = true;
      $storage = storage::get('files');
      if (is_null($selected) || (is_array($selected) && isset($selected['color__page_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__page_id',                   $preset->colors->color__page_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__text_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__text_id',                   $preset->colors->color__text_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__main_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__main_id',                   $preset->colors->color__main_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__link_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__link_id',                   $preset->colors->color__link_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__link_active_id'           ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__link_active_id',            $preset->colors->color__link_active_id,            false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__table_row_odd_id'         ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__table_row_odd_id',          $preset->colors->color__table_row_odd_id,          false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__table_row_even_id'        ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__table_row_even_id',         $preset->colors->color__table_row_even_id,         false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__relation_id'              ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__relation_id',               $preset->colors->color__relation_id,               false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__menu_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__menu_id',                   $preset->colors->color__menu_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__menu_active_id'           ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__menu_active_id',            $preset->colors->color__menu_active_id,            false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__menu_text_id'             ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__menu_text_id',              $preset->colors->color__menu_text_id,              false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__menu_link_id'             ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__menu_link_id',              $preset->colors->color__menu_link_id,              false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__menu_link_active_id'      ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__menu_link_active_id',       $preset->colors->color__menu_link_active_id,       false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__tabs_id'                  ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__tabs_id',                   $preset->colors->color__tabs_id,                   false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__tabs_link_id'             ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__tabs_link_id',              $preset->colors->color__tabs_link_id,              false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__tabs_link_active_id'      ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__tabs_link_active_id',       $preset->colors->color__tabs_link_active_id,       false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__tabs_link_active_no_bg_id']))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__tabs_link_active_no_bg_id', $preset->colors->color__tabs_link_active_no_bg_id, false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__ok_id'                    ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__ok_id',                     $preset->colors->color__ok_id,                     false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__warning_id'               ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__warning_id',                $preset->colors->color__warning_id,                false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__error_id'                 ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__error_id',                  $preset->colors->color__error_id,                  false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__fieldset_id'              ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__fieldset_id',               $preset->colors->color__fieldset_id,               false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__fieldset_nested_id'       ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__fieldset_nested_id',        $preset->colors->color__fieldset_nested_id,        false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__field_id'                 ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__field_id',                  $preset->colors->color__field_id,                  false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__field_text_id'            ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__field_text_id',             $preset->colors->color__field_text_id,             false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__button_id'                ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__button_id',                 $preset->colors->color__button_id,                 false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__button_active_id'         ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__button_active_id',          $preset->colors->color__button_active_id,          false);
      if (is_null($selected) || (is_array($selected) && isset($selected['color__button_text_id'           ]))) $result&= $storage->changes_insert('page', 'update', 'settings/page/color__button_text_id',            $preset->colors->color__button_text_id,            false);
      if ($reset) storage_nosql_files::cache_update();
      return $result;
    }
  }

  static function apply_with_custom_ids($selected = [], $reset = false) {
    $result = true;
    $storage = storage::get('files');
    foreach ($selected as $c_id_setting => $c_id_color)
      $result&= $storage->changes_insert('page', 'update', 'settings/page/'.$c_id_setting, $c_id_color, false);
    if ($reset) storage_nosql_files::cache_update();
    return $result;
  }

  static function reset() {
    $result = true;
    $storage = storage::get('files');
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__page_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__text_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__main_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__link_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__link_active_id',            false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__table_row_odd_id',          false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__table_row_even_id',         false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__relation_id',               false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__menu_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__menu_active_id',            false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__menu_text_id',              false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__menu_link_id',              false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__menu_link_active_id',       false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__tabs_id',                   false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__tabs_link_id',              false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__tabs_link_active_id',       false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__tabs_link_active_no_bg_id', false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__ok_id',                     false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__warning_id',                false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__error_id',                  false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__fieldset_id',               false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__fieldset_nested_id',        false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__field_id',                  false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__field_text_id',             false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__button_id',                 false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__button_active_id',          false);
    $result&= $storage->changes_delete('page', 'update', 'settings/page/color__button_text_id'                  );
    return $result;
  }

}}