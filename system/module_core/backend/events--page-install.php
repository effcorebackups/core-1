<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\tabs;
          abstract class events_page_install {

  static function on_init_tabs($page) {
    tabs::item_insert('English', 'language_select_en', 'language_select', 'en', null, ['class' => ['en' => 'en']]);
    tabs::item_insert('Russian', 'language_select_ru', 'language_select', 'ru', null, ['class' => ['ru' => 'ru']]);
  }

}}