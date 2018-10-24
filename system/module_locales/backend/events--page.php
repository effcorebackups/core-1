<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\language;
          use \effcore\markup;
          abstract class events_page {

  static function on_show_block_menu_languages($page) {
    $menu = new markup('x-languages');
    foreach (language::get_all() as $c_language) {
      $title = $c_language->code == 'en' ?
        $c_language->title->en :
        $c_language->title->en.' ('.$c_language->title->native.')';
      $href = $page->args_get('base').'/'.$c_language->code;
      $link = new markup('a', ['href' => $href], $title);
      $link_wrapper = new markup('x-language', ['data-code' => $c_language->code], $link);
      $menu->child_insert($link_wrapper, $c_language->code);
    }
    return $menu;
  }

}}