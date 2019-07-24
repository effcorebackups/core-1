<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\language;
          use \effcore\url;
          abstract class events_page_install {

  static function on_before_build($event, $page) {
    $languages = language::get_all();
    $code = $page->args_get('lang_code');
    if (!isset($languages[$code])) url::go($page->args_get('base').'/en');
    language::code_set_current($code);
  }

}}