<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\markup;
          use \effcore\table as table;
          use \effcore\table_body_row as table_body_row;
          use \effcore\table_body_row_cell as table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_classes($page) {
    return new table(['class' => ['classes' => 'classes']], [['UNDER CONSTRUCTION']]);
  }

  static function on_show_block_demo_dynamic($page) {
  # table
    $thead = [['th 1', 'th 2', 'th 3']];
    $tbody = [
      ['td 1.1', 'td 1.2', 'td 1.3'],
      ['td 2.1', 'td 2.2', new table_body_row_cell([], 'td 2.3')],
      new table_body_row([], ['td 3.1', 'td 3.2', new table_body_row_cell([], 'td 3.3')])
    ];
    return new markup('x-block', ['id' => 'demo_dynamic'], [
      new markup('h2', [], 'Dynamic block'),
      new table(['class' => ['table' => 'table']], $tbody, $thead)
    ]);
  }

}}