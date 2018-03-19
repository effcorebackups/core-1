<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\markup;
          use \effcore\message;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_demo_dynamic($page) {
    message::insert('Notice message #1.', 'notice');
    message::insert('Notice message #2.', 'notice');
    message::insert('Notice message #3.', 'notice');
    message::insert('Ok message #1 (default type).');
    message::insert('Ok message #2.');
    message::insert('Ok message #3.');
    message::insert('Warning message #1.', 'warning');
    message::insert('Warning message #2.', 'warning');
    message::insert('Warning message #3.', 'warning');
    message::insert('Error message #1.', 'error');
    message::insert('Error message #2.', 'error');
    message::insert('Error message #3.', 'error');
    $thead = [['head cell 1', 'head cell 2', 'head cell 3']];
    $tbody = [
      ['cell 1.1', 'cell 1.2',                                                    'cell 1.3'],
      ['cell 2.1', 'cell 2.2',                        new table_body_row_cell([], 'cell 2.3')],
      new table_body_row([], ['cell 3.1', 'cell 3.2', new table_body_row_cell([], 'cell 3.3')])
    ];
    return new markup('x-block', ['id' => 'demo_dynamic'], [
      new markup('h2', [], 'Dynamic block'),
      new table(['class' => ['table' => 'table']], $tbody, $thead)
    ]);
  }

}}