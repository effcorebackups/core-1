<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\block;
          use \effcore\canvas_svg;
          use \effcore\control_actions_list;
          use \effcore\decorator;
          use \effcore\diagram;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\node;
          use \effcore\pager;
          use \effcore\table_body_row_cell;
          use \effcore\table_body_row;
          use \effcore\table;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_page_demo {

  static function on_page_init($page) {
    $type = $page->args_get('type');
    if ($type == null) {
      url::go($page->args_get('base').'/embedded/form_elements');
    }
  }

  static function on_menu_demo_do_dynamic_changes($page) {
    tree::item_insert('item #3 (from code)',     'demo_item_3',     'M:demo',        '/develop/demo/embedded/menus/item_3',                     ['class' => ['demo-item-3'     => 'demo-item-3'    ]]);
    tree::item_insert('item #1.2.3 (from code)', 'demo_item_1_2_3', 'demo_item_1_2', '/develop/demo/embedded/menus/item_1/item_1_2/item_1_2_3', ['class' => ['demo-item-1-2-3' => 'demo-item-1-2-3']]);
  }

  ################
  ### messages ###
  ################

  static function on_show_demo_messages($page) {
    message::insert(translation::get('credentials'),                                  'credentials'                           );
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 1]), 'notice'                                );
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 2]), 'notice'                                );
    message::insert(translation::get('Notice message #%%_number.',  ['number' => 3]), 'notice'                                );
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 1]).' ('.translation::get('default type').')');
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 2])                                          );
    message::insert(translation::get('Ok message #%%_number.',      ['number' => 3])                                          );
    message::insert(translation::get('Warning message #%%_number.', ['number' => 1]), 'warning'                               );
    message::insert(translation::get('Warning message #%%_number.', ['number' => 2]), 'warning'                               );
    message::insert(translation::get('Warning message #%%_number.', ['number' => 3]), 'warning'                               );
    message::insert(translation::get('Error message #%%_number.',   ['number' => 1]), 'error'                                 );
    message::insert(translation::get('Error message #%%_number.',   ['number' => 2]), 'error'                                 );
    message::insert(translation::get('Error message #%%_number.',   ['number' => 3]), 'error'                                 );
  }

  ##############
  ### canvas ###
  ##############

  static function on_show_block_demo_canvas($page) {
    $canvas = new canvas_svg(105, 16, 5);
    $canvas->glyph_set('-XXX-|X---X|X---X|X---X|X---X|X---X|X---X|X---X|X---X|-XXX-',  5, 3); # 0
    $canvas->glyph_set('----X|---X-|--X-X|-X--X|X---X|----X|----X|----X|----X|----X', 15, 3); # 1
    $canvas->glyph_set('XXXX-|----X|----X|----X|----X|---X-|--X--|-X---|X----|XXXXX', 25, 3); # 2
    $canvas->glyph_set('XXXXX|----X|---X-|--X--|-X---|XXXXX|----X|---X-|--X--|-X---', 35, 3); # 3
    $canvas->glyph_set('----X|---X-|--X-X|-X--X|X---X|-XXXX|----X|----X|----X|----X', 45, 3); # 4
    $canvas->glyph_set('-XXXX|X----|X----|X----|X----|-XXXX|----X|---X-|--X--|-X---', 55, 3); # 5
    $canvas->glyph_set('---X-|--X--|-X---|X----|-XXX-|X---X|X---X|X---X|X---X|-XXX-', 65, 3); # 6
    $canvas->glyph_set('XXXXX|----X|---X-|--X--|-X---|X----|X----|X----|X----|X----', 75, 3); # 7
    $canvas->glyph_set('-XXX-|X---X|X---X|X---X|-XXX-|X---X|X---X|X---X|X---X|-XXX-', 85, 3); # 8
    $canvas->glyph_set('-XXX-|X---X|X---X|X---X|X---X|-XXX-|----X|---X-|--X--|-X---', 95, 3); # 9
    return new block('Canvas', ['class' => ['demo-canvas' => 'demo-canvas']], [
      $canvas
    ]);
  }

  ################
  ### diagrams ###
  ################

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

  ##################
  ### decorators ###
  ##################

  static function on_show_block_demo_decorators_dynamic($page) {
  # ─────────────────────────────────────────────────────────────────────
  # table
  # ─────────────────────────────────────────────────────────────────────
    $decorator_table_title = new markup('h3', [], 'Table');
    $decorator_table = new decorator('table');
    $decorator_table->id = 'demo_table';
    $decorator_table->visibility_rowid  = 'visible'; # visible | not_int | hidden
    $decorator_table->visibility_cellid = 'visible'; # visible | not_int | hidden
    $decorator_table->data = [
      'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                    'cell-1' => ['value' => 'cell #1.1', 'title' => 'head cell #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                    'cell-2' => ['value' => 'cell #1.2', 'title' => 'head cell #2'],
                    'cell-3' => ['value' => 'cell #1.3', 'title' => 'head cell #3']],
      'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                    'cell-1' => ['value' => 'cell #2.1'],
                    'cell-2' => ['value' => 'cell #2.2'],
                    'cell-3' => ['value' => 'cell #2.3']],
      'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                    'cell-1' => ['value' => 'cell #3.1'],
                    'cell-2' => ['value' => 'cell #3.2'],
                    'cell-3' => ['value' => 'cell #3.3']]];
  # ─────────────────────────────────────────────────────────────────────
  # ul (unordered list)
  # ─────────────────────────────────────────────────────────────────────
    $decorator_ul_title = new markup('h3', [], 'Unordered list');
    $decorator_ul = new decorator('ul');
    $decorator_ul->id = 'demo_ul';
    $decorator_ul->visibility_rowid  = 'visible'; # visible | not_int | hidden
    $decorator_ul->visibility_cellid = 'visible'; # visible | not_int | hidden
    $decorator_ul->data = [
      'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                    'field-1' => ['title' => 'Field 1.1', 'value' => 'Value 1.1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                    'field-2' => ['title' => 'Field 1.2', 'value' => 'Value 1.2'],
                    'field-3' => ['title' => 'Field 1.3', 'value' => 'Value 1.3']],
      'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                    'field-1' => ['title' => 'Field 2.1', 'value' => 'Value 2.1'],
                    'field-2' => ['title' => 'Field 2.2', 'value' => 'Value 2.2'],
                    'field-3' => ['title' => 'Field 2.3', 'value' => 'Value 2.3']],
      'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                    'field-1' => ['title' => 'Field 3.1', 'value' => 'Value 3.1'],
                    'field-2' => ['title' => 'Field 3.2', 'value' => 'Value 3.2'],
                    'field-3' => ['title' => 'Field 3.3', 'value' => 'Value 3.3']]];
  # ─────────────────────────────────────────────────────────────────────
  # dl (definition list)
  # ─────────────────────────────────────────────────────────────────────
    $decorator_dl_title = new markup('h3', [], 'Definition list');
    $decorator_dl = new decorator('dl');
    $decorator_dl->id = 'demo_dl';
    $decorator_dl->visibility_rowid  = 'visible'; # visible | not_int | hidden
    $decorator_dl->visibility_cellid = 'visible'; # visible | not_int | hidden
    $decorator_dl->data = [
      'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                    'field-1' => ['title' => 'Field 1.1', 'value' => 'Value 1.1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                    'field-2' => ['title' => 'Field 1.2', 'value' => 'Value 1.2'],
                    'field-3' => ['title' => 'Field 1.3', 'value' => 'Value 1.3']],
      'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                    'field-1' => ['title' => 'Field 2.1', 'value' => 'Value 2.1'],
                    'field-2' => ['title' => 'Field 2.2', 'value' => 'Value 2.2'],
                    'field-3' => ['title' => 'Field 2.3', 'value' => 'Value 2.3']],
      'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                    'field-1' => ['title' => 'Field 3.1', 'value' => 'Value 3.1'],
                    'field-2' => ['title' => 'Field 3.2', 'value' => 'Value 3.2'],
                    'field-3' => ['title' => 'Field 3.3', 'value' => 'Value 3.3']]];
  # ─────────────────────────────────────────────────────────────────────
  # result block
  # ─────────────────────────────────────────────────────────────────────
    return new block('Decorators dynamic', ['data-styled-title' => 'no', 'class' => ['demo-decorators-dynamic' => 'demo-decorators-dynamic']], [
      $decorator_table_title,
      $decorator_table,
      $decorator_ul_title,
      $decorator_ul,
      $decorator_dl_title,
      $decorator_dl
    ]);
  }

  ##############
  ### markup ###
  ##############

  static function on_show_block_demo_markup_dynamic($page) {
  # ─────────────────────────────────────────────────────────────────────
  # paragraph
  # ─────────────────────────────────────────────────────────────────────
    $paragraph_title = new markup('h3', [], 'Paragraph');
    $paragraph = new markup('p', [], ['content' => rtrim(str_repeat('Paragraph content. ', 16)).'&#10;', 'link_view_more' => new markup('a', ['href' => '/'], 'View more')]);
  # ─────────────────────────────────────────────────────────────────────
  # unordered list
  # ─────────────────────────────────────────────────────────────────────
    $unordered_list_title = new markup('h3', [], 'Unordered list');
    $unordered_list = new markup('ul', [], [
      'li_1'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 1])]),
      'li_2'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 2]),
        'li_2_ul'  => new markup('ul', [], [
          'li_2_1' => new markup('li', [], new text('item #%%_number', ['number' => 2.1])),
          'li_2_2' => new markup('li', [], new text('item #%%_number', ['number' => 2.2])),
          'li_2_3' => new markup('li', [], new text('item #%%_number', ['number' => 2.3]))])]),
      'li_3'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 3])])
    ]);
  # ─────────────────────────────────────────────────────────────────────
  # ordered list
  # ─────────────────────────────────────────────────────────────────────
    $ordered_list_title = new markup('h3', [], 'Ordered list');
    $ordered_list = new markup('ol', [], [
      'li_1'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 1])]),
      'li_2'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 2]),
        'li_2_ol'  => new markup('ol', [], [
          'li_2_1' => new markup('li', [], new text('item #%%_number', ['number' => 2.1])),
          'li_2_2' => new markup('li', [], new text('item #%%_number', ['number' => 2.2])),
          'li_2_3' => new markup('li', [], new text('item #%%_number', ['number' => 2.3]))])]),
      'li_3'       => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 3])])
    ]);
  # ─────────────────────────────────────────────────────────────────────
  # table (combinations of arrays and table_body_row and table_body_row_cell)
  # ─────────────────────────────────────────────────────────────────────
    $table_thead = [[
      'th_1' => translation::get('head cell #%%_number', ['number' => 1]),
      'th_2' => translation::get('head cell #%%_number', ['number' => 2]),
      'th_3' => translation::get('head cell #%%_number', ['number' => 3])
    ]];
    $table_tbody = [
      ['td_1' =>                             translation::get('cell #%%_number', ['number' => 1.1]),
       'td_2' =>                             translation::get('cell #%%_number', ['number' => 1.2]),
       'td_3' =>                             translation::get('cell #%%_number', ['number' => 1.3])],
      ['td_1' =>                             translation::get('cell #%%_number', ['number' => 2.1]),
       'td_2' =>                             translation::get('cell #%%_number', ['number' => 2.2]),
       'td_3' => new table_body_row_cell([], translation::get('cell #%%_number', ['number' => 2.3]))],
      new table_body_row([], [
       'td_1' =>                             translation::get('cell #%%_number', ['number' => 3.1]),
       'td_2' =>                             translation::get('cell #%%_number', ['number' => 3.2]),
       'td_3' => new table_body_row_cell([], translation::get('cell #%%_number', ['number' => 3.3]))]),
      new table_body_row([], [
       'td_1' => new table_body_row_cell(['colspan' => 3], new text(''))
      ])
    ];
    $table_title = new markup('h3', [], 'Table');
    $table = new table(['class' => ['table' => 'table']],
      $table_tbody,
      $table_thead
    );
  # ─────────────────────────────────────────────────────────────────────
  # pager
  # ─────────────────────────────────────────────────────────────────────
    $pager = new pager(1, 10000, 'my', 'pager');

  # ─────────────────────────────────────────────────────────────────────
  # control elements
  # ─────────────────────────────────────────────────────────────────────
    $controls_title = new markup('h3', [], 'Control elements');
  # actions list
    $actions_list = new control_actions_list('', ['class' => ['demo-actions-list' => 'demo-actions-list']]);
    $actions_list->actions = [
      'item_1' => 'item #1',
      'item_2' => 'item #2',
      'item_3' => 'item #3'
    ];
  # ─────────────────────────────────────────────────────────────────────
  # result block
  # ─────────────────────────────────────────────────────────────────────
    return new block('Markup dynamic', ['class' => ['demo-markup-dynamic' => 'demo-markup-dynamic']], [
      $paragraph_title,
      $paragraph,
      $unordered_list_title,
      $unordered_list,
      $ordered_list_title,
      $ordered_list,
      $table_title,
      $table,
      $pager,
      $controls_title,
      $actions_list
    ]);
  }

}}
