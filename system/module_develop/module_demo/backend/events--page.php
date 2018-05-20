<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\canvas_svg;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          use \effcore\translation;
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
    return new markup('x-block', ['class' => ['demo-dynamic']], [
      new markup('h2', [], 'Dynamic block'),
      new table(['class' => ['table' => 'table']], $tbody, $thead)
    ]);
  }

  static function on_show_block_demo_canvas($page) {
    $canvas = new canvas_svg(105, 16, 5);
    $canvas->glyph_set('01110|10001|10001|10001|10001|10001|10001|10001|10001|01110',  5, 3); # 0
    $canvas->glyph_set('00001|00001|00001|00001|00001|10001|01001|00101|00010|00001', 15, 3); # 1
    $canvas->glyph_set('11111|10000|01000|00100|00010|00001|00001|00001|00001|11110', 25, 3); # 2
    $canvas->glyph_set('01000|00100|00010|00001|11111|01000|00100|00010|00001|11111', 35, 3); # 3
    $canvas->glyph_set('00001|00001|00001|00001|01111|10001|01001|00101|00010|00001', 45, 3); # 4
    $canvas->glyph_set('01000|00100|00010|00001|01111|10000|10000|10000|10000|01111', 55, 3); # 5
    $canvas->glyph_set('01110|10001|10001|10001|10001|01110|10000|01000|00100|00010', 65, 3); # 6
    $canvas->glyph_set('10000|10000|10000|10000|10000|01000|00100|00010|00001|11111', 75, 3); # 7
    $canvas->glyph_set('01110|10001|10001|10001|10001|01110|10001|10001|10001|01110', 85, 3); # 8
    $canvas->glyph_set('01000|00100|00010|00001|01110|10001|10001|10001|10001|01110', 95, 3); # 9
    return new markup('x-block', ['class' => ['demo-canvas']], [
      new markup('h2', [], 'Canvas'),
      $canvas
    ]);
  }

}}