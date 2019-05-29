<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\text_simple;
          use \effcore\token;
          abstract class events_page_tokens {

  static function on_show_block_tokens($page) {
    $decorator = new decorator('table');
    $decorator->id = 'tokens_registered';
    $decorator->result_attributes = ['class' => ['compact' => 'compact']];
    $tokens = token::get_all();
    ksort($tokens);
    foreach ($tokens as $c_row_id => $c_token) {
      $decorator->data[] = [
        'rowid'     => ['value' => new text_simple($c_row_id          ), 'title' => 'Row ID'   ],
        'match'     => ['value' => new text_simple($c_token->match    ), 'title' => 'Match'    ],
        'type'      => ['value' => new text_simple($c_token->type     ), 'title' => 'Type'     ],
        'module_id' => ['value' => new text_simple($c_token->module_id), 'title' => 'Module ID']
      ];
    }
    return new block('', ['data-id' => 'tokens_registered'], [
      $decorator
    ]);
  }

}}
