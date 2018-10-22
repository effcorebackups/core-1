<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\language;
          abstract class events_page {

  static function on_switch_language($page) {
    $languages = language::get_all();
    $code = $page->args_get('lang_code');
    if (!isset($languages[$code])) url::go($page->args_get('base').'/en');
    language::current_code_set($code);
  }

}}