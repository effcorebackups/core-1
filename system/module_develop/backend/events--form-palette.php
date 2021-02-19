<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\color;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\storage_nosql_files;
          use \effcore\text;
          abstract class events_form_palette {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('develop');
    $base_color = new color(null, $settings->test_color, $settings->test_color);
    $palette_markup = [];
    $palette_colors = [];
    for ($i = 0; $i < 21; $i++) {
      $c_color_hex = $base_color->filter_shift(($i - 10) * 5, ($i - 10) * 5, ($i - 10) * 5, 1, color::return_hex);
      $c_color_key = 'custom_'.ltrim($c_color_hex, '#');
      $palette_colors[$c_color_key] = new color(null, $c_color_hex, $c_color_hex, 'custom');
      $palette_markup[] = new markup('x-color');
    }
    $items['#color']->value_set($settings->test_color);
    $items['palette/result']->child_select('palette')->child_insert(new markup('x-palette', [], $palette_markup), 'palette');
    $items['palette/result']->child_select('data'   )->child_insert(new text(storage_nosql_files::data_to_text($palette_colors, 'colors')), 'data');
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'generate':
        storage::get('files')->changes_insert('develop', 'update', 'settings/develop/test_color', $items['#color']->value_get());
        message::insert('Generation done.');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}