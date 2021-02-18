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
    $colors_via_parametric_tokens = [];
    $colors_via_overlays          = [];
    for ($i = 0; $i < 21; $i++) {
      $colors_via_parametric_tokens[] = new markup('x-color', []                               );
      $colors_via_overlays         [] = new markup('x-color', [], new markup('x-color-overlay')); }
    $items['palette/result']->child_insert(new markup('x-colors-group', ['via-parametric-tokens' => true], $colors_via_parametric_tokens), 'colors_via_parametric_tokens');
    $items['palette/result']->child_insert(new markup('x-colors-group', ['via-overlays'          => true], $colors_via_overlays),          'colors_via_overlays');
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