<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\translation_ru {
          use \effcore\language;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\text;
          abstract class events_module {

  static function on_enable() {
    $module = module::get('translation_ru');
    $module->enable();
    message::insert(new text('Translations for language %%_name was inserted.', ['name' => language::get('ru')->title_en]));
    message::insert(new text('Language %%_name was inserted.',                  ['name' => language::get('ru')->title_en]));
    message::insert(new text('Language %%_name was enabled.',                   ['name' => language::get('ru')->title_en]));
    storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', 'ru');
    language::code_set_current('ru');
  }

  static function on_disable() {
    $module = module::get('translation_ru');
    $module->disable();
    storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', 'en');
    language::code_set_current('en');
  }

}}