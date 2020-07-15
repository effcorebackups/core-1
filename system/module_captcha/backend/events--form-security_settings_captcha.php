<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\canvas_svg;
          use \effcore\core;
          use \effcore\field_captcha;
          use \effcore\field_switcher;
          use \effcore\frontend;
          use \effcore\glyph;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\text;
          abstract class events_form_security_settings_captcha {

  static function on_init($event, $form, $items) {
    if (!frontend::select('captcha_form'))
         frontend::insert('captcha_form', null, 'styles', ['path' => 'frontend/captcha.css', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'form_style', 'captcha');
    $settings = module::settings_get('captcha');
    $items['#length']->value_set($settings->captcha_length);
    $form->glyphs = glyph::get_all();
    core::array_sort_text($form->glyphs);
    foreach ($form->glyphs as $c_glyph => $c_character) {
      $c_item = new markup('x-glyph');
      $c_canvas = new canvas_svg(7, 12, 6);
      $c_canvas->glyph_set($c_glyph, 1, 1);
      $c_switcher = new field_switcher;
      $c_switcher->build();
      $c_switcher->name_set('is_enabled_glyph[]');
      $c_switcher->value_set($c_glyph);
      $c_switcher->checked_set($settings->captcha_glyphs === null || isset($settings->captcha_glyphs[$c_glyph]));
      $c_item->child_insert($c_canvas, 'canvas');
      $c_item->child_insert($c_switcher, 'switcher');
      $items['main/glyphs']->child_insert($c_item, $c_glyph);
    }
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $captcha_glyphs = [];
        foreach ($form->glyphs as $c_glyph => $c_character)
          if ($items['#is_enabled_glyph:'.$c_glyph]->checked_get())
            $captcha_glyphs[$c_glyph] = $c_character;
        if (count($captcha_glyphs) === 0) {
          $form->error_set('Group "%%_title" should contain at least one selected item!', ['title' => (new text($items['main/glyphs']->title))->render()]);
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $captcha_glyphs = [];
        foreach ($form->glyphs as $c_glyph => $c_character)
          if ($items['#is_enabled_glyph:'.$c_glyph]->checked_get())
            $captcha_glyphs[$c_glyph] = $c_character;
        storage::get('files')->changes_insert('captcha', 'update', 'settings/captcha/captcha_glyphs', $captcha_glyphs);
        storage::get('files')->changes_insert('captcha', 'update', 'settings/captcha/captcha_length', $items['#length']->value_get());
        field_captcha::captcha_cleaning();
        message::insert('The changes was saved.');
        break;
    }
  }

}}