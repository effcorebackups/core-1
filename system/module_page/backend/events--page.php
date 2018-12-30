<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\markup;
          use \effcore\text;
use \effcore\token;
use \effcore\translation;
          abstract class events_page {

  static function on_show_title($page) {
    // $title = new text($page->title);
    // $title->is_apply_tokens = true;
    // return new markup('h1', ['id' => 'title'], $title);
    return new markup('h1', ['id' => 'title'],
      token::replace(translation::get($page->title))
    );
  }

}}