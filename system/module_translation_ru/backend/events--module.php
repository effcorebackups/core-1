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

  static function on_enable($event) {
    if ((page::get_current()->id === 'install' && language::code_get_current() === 'ru') ||
        (page::get_current()->id !== 'install')) {
      $module = module::get('translation_ru');
      $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('translation_ru');
    $module->disable();
    if (language::code_get_current() === 'ru') {
      storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code', 'en');
      language::code_set_current('en');
    }
  }

}}