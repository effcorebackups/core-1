<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_palette {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('develop');
    $items['#color']->value_set($settings->test_color);
    $colors_markup = [];
    for ($i = 0; $i < 21; $i++) $colors_markup[] = new markup('x-color');
    $items['palette/result']->child_insert(
      new markup('x-colors-group', [], $colors_markup), 'colors'
    );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'generate':
        storage::get('files')->changes_insert('develop', 'update', 'settings/develop/test_color', $items['#color']->value_get());
        message::insert('Generation done.');
        break;
    }
  }

}}