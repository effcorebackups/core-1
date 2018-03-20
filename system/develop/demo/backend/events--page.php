<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\markup;
          use \effcore\message;
          use \effcore\translation;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_demo_dynamic($page) {
    message::insert(translation::get('Notice message #%%_num.', ['num' => 1]), 'notice');
    message::insert(translation::get('Notice message #%%_num.', ['num' => 2]), 'notice');
    message::insert(translation::get('Notice message #%%_num.', ['num' => 3]), 'notice');
    message::insert(translation::get('Ok message #%%_num.', ['num' => 1]).' ('.translation::get('default type').')');
    message::insert(translation::get('Ok message #%%_num.', ['num' => 2]));
    message::insert(translation::get('Ok message #%%_num.', ['num' => 3]));
    message::insert(translation::get('Warning message #%%_num.', ['num' => 1]), 'warning');
    message::insert(translation::get('Warning message #%%_num.', ['num' => 2]), 'warning');
    message::insert(translation::get('Warning message #%%_num.', ['num' => 3]), 'warning');
    message::insert(translation::get('Error message #%%_num.', ['num' => 1]), 'error');
    message::insert(translation::get('Error message #%%_num.', ['num' => 2]), 'error');
    message::insert(translation::get('Error message #%%_num.', ['num' => 3]), 'error');
    $thead = [[
      translation::get('head cell %%_num', ['num' => 1]),
      translation::get('head cell %%_num', ['num' => 2]),
      translation::get('head cell %%_num', ['num' => 3])
    ]];
    $tbody = [
      [translation::get('cell %%_num', ['num' => 1.1]),
       translation::get('cell %%_num', ['num' => 1.2]),
       translation::get('cell %%_num', ['num' => 1.3])],
      [translation::get('cell %%_num', ['num' => 2.1]),
       translation::get('cell %%_num', ['num' => 2.2]), new table_body_row_cell([],
       translation::get('cell %%_num', ['num' => 2.3]))],
      new table_body_row([], [translation::get('cell %%_num', ['num' => 3.1]),
                              translation::get('cell %%_num', ['num' => 3.2]), new table_body_row_cell([],
                              translation::get('cell %%_num', ['num' => 3.3]))])
    ];
    return new markup('x-block', ['id' => 'demo_dynamic'], [
      new markup('h2', [], 'Dynamic block'),
      new table(['class' => ['table' => 'table']], $tbody, $thead)
    ]);
  }

}}