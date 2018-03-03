<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\markup;
          use \effcore\factory;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_classes($page) {
    $thead = [['type', 'name', 'file']];
    $tbody = [];
    foreach (factory::get_classes_map() as $c_class_name => $c_class_info) {
      $tbody[] = [
        new table_body_row_cell(['class' => ['type' => 'type']], $c_class_info->type == 'interface' ? 'intr.' : $c_class_info->type),
        new table_body_row_cell(['class' => ['name' => 'name']], $c_class_info->namespace.' \ '.$c_class_info->name),
        new table_body_row_cell(['class' => ['file' => 'file']], $c_class_info->file)
      ];
    }
    return new markup('x-block', ['id' => 'classes_list'], [
      new markup('h2', [], 'Classes list'),
      new table(['class' => ['classes-list' => 'classes-list']], $tbody, $thead)
    ]);
  }

  static function on_show_block_demo_dynamic($page) {
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