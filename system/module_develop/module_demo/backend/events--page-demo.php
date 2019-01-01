<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\block;
          use \effcore\canvas_svg;
          use \effcore\diagram;
          use \effcore\message;
          use \effcore\node;
          use \effcore\table_body_row_cell;
          use \effcore\table_body_row;
          use \effcore\table;
          use \effcore\tabs;
          use \effcore\translation;
          use \effcore\tree;
          abstract class events_page_demo {

  static function on_show_demo_messages($page) {
    message::insert(translation::get('credentials'), 'credentials');
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 1]), 'notice');
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 2]), 'notice');
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 3]), 'notice');
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 1]).' ('.translation::get('default type').')');
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 2]));
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 3]));
    message::insert(translation::get('Warning message #%%_number.', ['number' => 1]), 'warning');
    message::insert(translation::get('Warning message #%%_number.', ['number' => 2]), 'warning');
    message::insert(translation::get('Warning message #%%_number.', ['number' => 3]), 'warning');
    message::insert(translation::get('Error message #%%_number.',   ['number' => 1]), 'error');
    message::insert(translation::get('Error message #%%_number.',   ['number' => 2]), 'error');
    message::insert(translation::get('Error message #%%_number.',   ['number' => 3]), 'error');
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
    return new block('Canvas', ['class' => ['demo-canvas' => 'demo-canvas']], [
      $canvas
    ]);
  }

  static function on_show_block_demo_diagrams($page) {
    $diagram_linear = new diagram('Title', 'linear');
    $diagram_linear->slice_add('Parameter 1', 70, '0.07 sec.');
    $diagram_linear->slice_add('Parameter 2', 20, '0.02 sec.');
    $diagram_linear->slice_add('Parameter 3', 10, '0.01 sec.');
    $diagram_radial = new diagram('Title', 'radial');
    $diagram_radial->slice_add('Parameter 1', 40, '0.04 sec.', '#216ce4');
    $diagram_radial->slice_add('Parameter 2', 30, '0.03 sec.', '#30c432');
    $diagram_radial->slice_add('Parameter 3', 20, '0.02 sec.', '#fc5740');
    $diagram_radial->slice_add('Parameter 4', 10, '0.01 sec.', '#fd9a1e');
    return new node([], [
      new block('Linear diagram', ['class' => ['demo-diagram-linear' => 'demo-diagram-linear']], $diagram_linear),
      new block('Radial diagram', ['class' => ['demo-diagram-radial' => 'demo-diagram-radial']], $diagram_radial)
    ]);
  }

  static function on_show_block_demo_dynamic_elements($page) {
    tabs::item_insert('item #3 (from code)',     'demo_item_3',     'T:demo',        'item_3',                     null,         ['class' => ['demo-item-3'     => 'demo-item-3']]);
    tabs::item_insert('item #1.2.3 (from code)', 'demo_item_1_2_3', 'demo_item_1_2', 'item_1/item_1_2/item_1_2_3', null,         ['class' => ['demo-item-1-2-3' => 'demo-item-1-2-3']]);
    tree::item_insert('item #3 (from code)',     'demo_item_3',     'M:demo',        '/develop/demo/item_3',                     ['class' => ['demo-item-3'     => 'demo-item-3']]);
    tree::item_insert('item #1.2.3 (from code)', 'demo_item_1_2_3', 'demo_item_1_2', '/develop/demo/item_1/item_1_2/item_1_2_3', ['class' => ['demo-item-1-2-3' => 'demo-item-1-2-3']]);
    $thead = [[
      translation::get('head cell #%%_number', ['number' => 1]),
      translation::get('head cell #%%_number', ['number' => 2]),
      translation::get('head cell #%%_number', ['number' => 3])
    ]];
    $tbody = [
      [translation::get('cell #%%_number', ['number' => 1.1]),
       translation::get('cell #%%_number', ['number' => 1.2]),
       translation::get('cell #%%_number', ['number' => 1.3])],
      [translation::get('cell #%%_number', ['number' => 2.1]),
       translation::get('cell #%%_number', ['number' => 2.2]), new table_body_row_cell([],
       translation::get('cell #%%_number', ['number' => 2.3]))],
      new table_body_row([], [translation::get('cell #%%_number', ['number' => 3.1]),
                              translation::get('cell #%%_number', ['number' => 3.2]), new table_body_row_cell([],
                              translation::get('cell #%%_number', ['number' => 3.3]))])
    ];
    return new block('Dynamic elements', ['data-styled-title' => 'no', 'class' => ['demo-dynamic' => 'demo-dynamic']], [
      new table(['class' => ['table' => 'table']], $tbody, $thead)
    ]);
  }

}}
