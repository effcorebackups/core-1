<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\canvas_svg;
          use \effcore\diagram;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\table_body_row_cell;
          use \effcore\table_body_row;
          use \effcore\table;
          use \effcore\translation;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_demo_dynamic($page) {
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
    return new markup('x-block', ['class' => ['demo-dynamic' => 'demo-dynamic']], [
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
    return new markup('x-block', ['class' => ['demo-canvas' => 'demo-canvas']], [
      new markup('h2', [], 'Canvas'),
      $canvas
    ]);
  }

  static function on_show_block_demo_diagrams($page) {
    $l_diagram = new diagram('Linear diagram', 'linear');
    $l_diagram->slice_add('Parameter 1', 40, '0.04 sec.');
    $l_diagram->slice_add('Parameter 2', 30, '0.03 sec.');
    $l_diagram->slice_add('Parameter 3', 20, '0.02 sec.');
    $l_diagram->slice_add('Parameter 4', 10, '0.01 sec.');
    $c_diagram = new diagram('Radial diagram', 'radial');
    $c_diagram->slice_add('Parameter 1', 40, '0.04 sec.', '#216ce4');
    $c_diagram->slice_add('Parameter 2', 30, '0.03 sec.', '#30c432');
    $c_diagram->slice_add('Parameter 3', 20, '0.02 sec.', '#fc5740');
    $c_diagram->slice_add('Parameter 4', 10, '0.01 sec.', '#fd9a1e');
    return new markup('x-block', ['class' => ['demo-diagrams' => 'demo-diagrams']], [
      new markup('h2', [], 'Diagrams'),
      $l_diagram,
      $c_diagram
    ]);
  }

}}
