<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\language;
          use \effcore\storage;
          use \effcore\url;
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
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', $items['#language']->value_get());
        url::go('/manage/locales');
        break;
    }
  }

}}