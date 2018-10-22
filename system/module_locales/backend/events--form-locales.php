<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\language;
          use \effcore\storage;
          abstract class events_form_locales {

  static function on_init($form, $items) {
    $languages = language::get_all();
    foreach ($languages as $c_language) {
      $title = $c_language->code == 'en' ?
        $c_language->title->en :
        $c_language->title->en.' ('.$c_language->title->native.')';
      $items['#language']->option_insert($title, $c_language->code);
    }
    $items['#language']->value_set(
      language::current_code_get()
    );
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $code_new = $items['#language']->value_get();
        language::current_code_set($code_new);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', $code_new);
        break;
    }
  }

}}