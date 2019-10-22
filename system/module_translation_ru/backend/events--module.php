<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\translation_ru {
          use \effcore\language;
          use \effcore\message;
          use \effcore\module;
          use \effcore\page;
          use \effcore\storage;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_module {

  static function on_enable() {
    if ((page::get_current()->args_get('base') == '/install' && language::code_get_current() == 'ru') ||
        (page::get_current()->args_get('base') != '/install')) {
      $module = module::get('translation_ru');
      $module->enable();
      message::insert(
        new text('You can enable or disable the language "%%_name" on page "%%_page".', ['name' => language::get('ru')->title_en, 'page' => translation::get('Locales')])
      );
    }
  }

  static function on_disable() {
    $module = module::get('translation_ru');
    $module->disable();
    if (language::code_get_current() == 'ru') {
      storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', 'en');
      language::code_set_current('en');
    }
  }

}}