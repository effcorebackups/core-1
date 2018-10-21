<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\language;
          use \effcore\tabs;
          use \effcore\url;
          abstract class events_page_install {

  static function on_init_languages($page) {
    $languages = language::get_all();
    $code = $page->args_get('code');
    if (!isset($languages[$code])) url::go($page->args_get('base').'/'.reset($languages)->code);
    language::current_code_set($code);
    foreach ($languages as $c_language) {
      tabs::item_insert(   $c_language->title->en.' ('.$c_language->title->native.')',
        'language_select_'.$c_language->code,
        'language_select', $c_language->code, null, ['class' => [$c_language->code => $c_language->code]]
      );
    }
  }

}}