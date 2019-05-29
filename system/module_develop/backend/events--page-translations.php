<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\language;
          use \effcore\text_simple;
          use \effcore\translation;
          abstract class events_page_translations {

  static function on_show_block_translations($page) {
    $decorator = new decorator('table');
    $decorator->id = 'translations_registered';
    $decorator->result_attributes = ['data-is-compact' => 'true'];
    $translations = translation::get_all_by_code();
    ksort($translations);
    foreach ($translations as $c_orig => $c_tran) {
      $decorator->data[] = [
        'orig' => ['value' => new text_simple($c_orig), 'title' => 'Original'   ],
        'tran' => ['value' => new text_simple($c_tran), 'title' => 'Translation']
      ];
    }
    return new block('', ['data-id' => 'translations_registered'], [
      $decorator
    ]);
  }

}}
