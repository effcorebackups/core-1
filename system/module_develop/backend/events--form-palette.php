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
          use \effcore\text_multiline;
          abstract class events_form_palette {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('develop');
    $base_hex = $settings->test_color;
    $items['#color']->value_set($base_hex);
    $palette_markup = [];
    $palette_colors = [];
    for ($i = 0; $i < 21; $i++) {
      $palette_markup[] = (new markup('x-color'));
      $palette_colors[] = (new color(null, $base_hex, $base_hex))->filter_shift(($i - 10) * 5, ($i - 10) * 5, ($i - 10) * 5, 1, color::return_hex);
    }
    $items['palette/result']->child_select('palette')->child_insert(new markup('x-palette', [], $palette_markup), 'palette');
    $items['palette/result']->child_select('data'   )->child_insert(new text_multiline($palette_colors),          'data');
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