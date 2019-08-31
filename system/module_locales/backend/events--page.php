<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\language;
          use \effcore\markup;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_page {

  static function on_show_block_menu_languages($page) {
    $menu = new markup('x-languages');
    foreach (language::get_all() as $c_language) {
      $c_title = $c_language->code == 'en' ?
        $c_language->title->en :
        $c_language->title->en.   ' ('.
        $c_language->title->native.')';
      $c_href = $page->args_get('base').'/'.$c_language->code;
      if (url::is_active($c_href))
           $c_link = new markup('a', ['href' => $c_href, 'title' => translation::get('go to %%_title language', ['title' => $c_language->title->en], 'en'), 'aria-selected' => 'true'], $c_title);
      else $c_link = new markup('a', ['href' => $c_href, 'title' => translation::get('go to %%_title language', ['title' => $c_language->title->en], 'en')                           ], $c_title);
      $menu->child_insert(new markup('x-language', ['data-code' => $c_language->code], $c_link), $c_language->code);
    }
    return $menu;
  }

}}