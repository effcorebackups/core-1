<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\color;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\storage_nosql_files;
          use \effcore\text;
          abstract class events_form_palette {

  static function on_init($event, $form, $items) {
    $items['palette/report']->child_select('data')->child_insert(
      new text('The report will be created after submitting the form.'), 'data'
    );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'generate':
        $base_color = new color(null, $items['#color']->value_get(), $items['#color']->value_get());
        $palette_markup = [];
        $palette_colors = [];
        for ($i = 0; $i < 21; $i++) {
          $c_offset = ($i - 10) * 5;
          $c_color_hex = $base_color->filter_shift($c_offset, $c_offset, $c_offset, 1, color::return_hex);
          $c_color_key = $items['#prefix']->value_get().($c_offset < 0 ? '-' : '+').str_pad(abs($c_offset), 2, '0', STR_PAD_LEFT);
          $palette_colors[$c_color_key] = new color(null, $c_color_hex, $c_color_hex, $items['#group']->value_get());
          $palette_markup[$c_color_key] = new markup('x-color', ['style' => 'background-color: '.$c_color_hex]); }
        $items['palette/report']->child_select('palette')->child_insert(new markup('x-palette', [], $palette_markup), 'palette');
        $items['palette/report']->child_select('data'   )->child_insert(new text(storage_nosql_files::data_to_text($palette_colors, 'colors')), 'data');
        message::insert('Generation done.');
        break;
    }
  }

}}