<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\translation_ru {
          use \effcore\language;
          use \effcore\module;
          use \effcore\page;
          use \effcore\storage;
          abstract class events_module {

  static function on_enable($event) {
    if ( (page::get_current()->id === 'install' && language::code_get_current() === 'ru') ||
         (page::get_current()->id !== 'install') ) {
      $module = module::get('translation_ru');
      $module->enable();
    }
  }

  static function on_disable($event) {
    $module = module::get('translation_ru');
    $module->disable();
    if (language::code_get_current() === 'ru') {
      storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', 'en');
      language::code_set_current('en');
    }
  }

}}