<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\language;
          use \effcore\url;
          abstract class events_page_install {

  static function on_page_init($page) {
    $languages = language::get_all();
    $code = $page->get_args('lang_code');
    if (!isset($languages[$code])) url::go($page->get_args('base').'/en');
    language::current_code_set($code);
  }

}}