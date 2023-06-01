<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\canvas_svg;
use effcore\decorator;
use effcore\diagram;
use effcore\locale;
use effcore\markup;
use effcore\message;
use effcore\node;
use effcore\tab_item;
use effcore\table_body_row_cell;
use effcore\table_body_row;
use effcore\table;
use effcore\text_multiline;
use effcore\text;
use effcore\tree_item;
use effcore\url;

abstract class events_page_demo {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        if ($type === null) {
            $items = tab_item::select_all(null, 'demo_embedded');
            url::go($page->args_get('base').'/'.reset($items)->action_name);
        }
    }

    static function on_tree_build_before_nosql($event, $tree) {
        tree_item::insert('item #1.2.3 (from code)', 'demo_nosql_item_1_2_3', 'demo_nosql_item_1_2', 'demo_nosql', '/develop/demo/embedded/trees/item_1/item_1_2/item_1_2_3', null, [], [], -10, 'demo');
        tree_item::insert('item #3 (from code)',     'demo_nosql_item_3',      null,                 'demo_nosql', '/develop/demo/embedded/trees/item_3',                     null, [], [], -10, 'demo');
    }

    static function on_tree_build_before_sql($event, $tree) {
        tree_item::insert('item #1.2.3 (from code)', 'demo_sql_item_1_2_3',   'demo_sql_item_1_2',   'demo_sql',   '/develop/demo/embedded/trees/item_1/item_1_2/item_1_2_3', null, [], [], -10, 'demo');
        tree_item::insert('item #3 (from code)',     'demo_sql_item_3',        null,                 'demo_sql',   '/develop/demo/embedded/trees/item_3',                     null, [], [], -10, 'demo');
    }

    ##############
    ### markup ###
    ##############

    static function block_markup__demo_markup_dynamic($page, $args = []) {

        # ─────────────────────────────────────────────────────────────────────
        # headers
        # ─────────────────────────────────────────────────────────────────────

        $header_h2 = new markup('h2', [], [new text('Header %%_size', ['size' => 'H2'])]);
        $header_h3 = new markup('h3', [], [new text('Header %%_size', ['size' => 'H3'])]);
        $header_h2_paragraph = new markup('p', [], ['content' => rtrim(str_repeat('Paragraph content. ', 16)).'&NewLine;',                                                                  ]);
        $header_h3_paragraph = new markup('p', [], ['content' => rtrim(str_repeat('Paragraph content. ', 16)).'&NewLine;', 'link_view_more' => new markup('a', ['href' => '/'], 'View more')]);

        # ─────────────────────────────────────────────────────────────────────
        # unordered list
        # ─────────────────────────────────────────────────────────────────────

        $list_title = new markup('h2', [], 'Lists');
        $list_unordered_title = new markup('h3', [], 'Unordered list');
        $list_unordered = new markup('ul', [], [
            'li_1'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 1])]),
            'li_2'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 2]),
                'li_2_ul'    => new markup('ul', [], [
                    'li_2_1' => new markup('li', [], new text('item #%%_number', ['number' => 2.1])),
                    'li_2_2' => new markup('li', [], new text('item #%%_number', ['number' => 2.2])),
                    'li_2_3' => new markup('li', [], new text('item #%%_number', ['number' => 2.3]))])]),
            'li_3'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 3])])
        ]);

        # ─────────────────────────────────────────────────────────────────────
        # ordered list
        # ─────────────────────────────────────────────────────────────────────

        $list_ordered_title = new markup('h3', [], 'Ordered list');
        $list_ordered = new markup('ol', [], [
            'li_1'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 1])]),
            'li_2'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 2]),
                'li_2_ol'    => new markup('ol', [], [
                    'li_2_1' => new markup('li', [], new text('item #%%_number', ['number' => 2.1])),
                    'li_2_2' => new markup('li', [], new text('item #%%_number', ['number' => 2.2])),
                    'li_2_3' => new markup('li', [], new text('item #%%_number', ['number' => 2.3]))])]),
            'li_3'           => new markup('li', [], ['content' => new text('item #%%_number', ['number' => 3])])
        ]);

        # ─────────────────────────────────────────────────────────────────────
        # table (combinations of arrays and table_body_row and table_body_row_cell)
        # ─────────────────────────────────────────────────────────────────────

        $table_thead = [[
            'th_1' => new text('head cell #%%_number', ['number' => 1]),
            'th_2' => new text('head cell #%%_number', ['number' => 2]),
            'th_3' => new text('head cell #%%_number', ['number' => 3])
        ]];
        $table_tbody = [
            [   'td_1' =>                             new text('cell #%%_number', ['number' => 1.1]),
                'td_2' =>                             new text('cell #%%_number', ['number' => 1.2]),
                'td_3' =>                             new text('cell #%%_number', ['number' => 1.3])],
            [   'td_1' =>                             new text('cell #%%_number', ['number' => 2.1]),
                'td_2' =>                             new text('cell #%%_number', ['number' => 2.2]),
                'td_3' => new table_body_row_cell([], new text('cell #%%_number', ['number' => 2.3]))],
            new table_body_row([], [
                'td_1' =>                             new text('cell #%%_number', ['number' => 3.1]),
                'td_2' =>                             new text('cell #%%_number', ['number' => 3.2]),
                'td_3' => new table_body_row_cell([], new text('cell #%%_number', ['number' => 3.3]))]),
            new table_body_row([], [
                'td_1' => new table_body_row_cell(['colspan' => 3], new text(''))
            ])
        ];
        $table_title = new markup('h2', [], 'Table');
        $table = new table(['class' => ['table' => 'table']],
            $table_tbody,
            $table_thead
        );

        # ─────────────────────────────────────────────────────────────────────
        # result block
        # ─────────────────────────────────────────────────────────────────────

        return new node([], [
            $header_h2,
            $header_h2_paragraph,
            $header_h3,
            $header_h3_paragraph,
            $list_title,
            $list_unordered_title,
            $list_unordered,
            $list_ordered_title,
            $list_ordered,
            $table_title,
            $table
        ]);
    }

    ##################
    ### decorators ###
    ##################

    static function block_markup__demo_decorators_dynamic($page, $args = []) {

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table
        # ─────────────────────────────────────────────────────────────────────

        $decorator_table_title = new markup('h3', [], 'view_type = table');
        $decorator_table = new decorator('table');
        $decorator_table->id = 'demo_table';
        $decorator_table->visibility_rowid  = 'visible'; # visible | not_int | hidden
        $decorator_table->visibility_cellid = 'visible'; # visible | not_int | hidden
        $decorator_table->data = [
            'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                'cell-1' => ['value' => 'value 1.1', 'title' => 'Field #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                'cell-2' => ['value' => 'value 1.2', 'title' => 'Field #2'],
                'cell-3' => ['value' => 'value 1.3', 'title' => 'Field #3']],
            'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                'cell-1' => ['value' => 'value 2.1'],
                'cell-2' => ['value' => 'value 2.2'],
                'cell-3' => ['value' => 'value 2.3']],
            'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                'cell-1' => ['value' => 'value 3.1'],
                'cell-2' => ['value' => 'value 3.2'],
                'cell-3' => ['value' => ''         ]]];

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table-adaptive
        # ─────────────────────────────────────────────────────────────────────

        $decorator_table_adaptive_title = new markup('h3', [], 'view_type = table-adaptive');
        $decorator_table_adaptive = new decorator('table-adaptive');
        $decorator_table_adaptive->id = 'demo_table_adaptive';
        $decorator_table_adaptive->visibility_rowid  = 'visible'; # visible | not_int | hidden
        $decorator_table_adaptive->visibility_cellid = 'visible'; # visible | not_int | hidden
        $decorator_table_adaptive->data = [
            'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                'cell-1' => ['value' => 'value 1.1', 'title' => 'Field #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                'cell-2' => ['value' => 'value 1.2', 'title' => 'Field #2'],
                'cell-3' => ['value' => 'value 1.3', 'title' => 'Field #3']],
            'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                'cell-1' => ['value' => 'value 2.1'],
                'cell-2' => ['value' => 'value 2.2'],
                'cell-3' => ['value' => 'value 2.3']],
            'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                'cell-1' => ['value' => 'value 3.1'],
                'cell-2' => ['value' => 'value 3.2'],
                'cell-3' => ['value' => ''         ]] ];

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table-dl
        # ─────────────────────────────────────────────────────────────────────

        $decorator_table_dl_title = new markup('h3', [], 'view_type = table-dl');
        $decorator_table_dl = new decorator('table-dl');
        $decorator_table_dl->id = 'demo_table_dl';
        $decorator_table_dl->visibility_rowid  = 'visible'; # visible | not_int | hidden
        $decorator_table_dl->visibility_cellid = 'visible'; # visible | not_int | hidden
        $decorator_table_dl->data = [
            'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                'cell-1' => ['value' => 'value 1.1', 'title' => 'Field #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                'cell-2' => ['value' => 'value 1.2', 'title' => 'Field #2'],
                'cell-3' => ['value' => 'value 1.3', 'title' => 'Field #3']],
            'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                'cell-1' => ['value' => 'value 2.1'],
                'cell-2' => ['value' => 'value 2.2'],
                'cell-3' => ['value' => 'value 2.3']],
            'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                'cell-1' => ['value' => 'value 3.1'],
                'cell-2' => ['value' => 'value 3.2'],
                'cell-3' => ['value' => ''         ]]];

        # ─────────────────────────────────────────────────────────────────────
        # view_type = ul
        # ─────────────────────────────────────────────────────────────────────

        $decorator_ul_title = new markup('h3', [], 'view_type = ul');
        $decorator_ul = new decorator('ul');
        $decorator_ul->id = 'demo_ul';
        $decorator_ul->visibility_rowid  = 'visible'; # visible | not_int | hidden
        $decorator_ul->visibility_cellid = 'visible'; # visible | not_int | hidden
        $decorator_ul->data = [
            'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                'field-1' => ['value' => 'value 1.1', 'title' => 'Field #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                'field-2' => ['value' => 'value 1.2', 'title' => 'Field #2'],
                'field-3' => ['value' => 'value 1.3', 'title' => 'Field #3']],
            'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                'field-1' => ['value' => 'value 2.1'],
                'field-2' => ['value' => 'value 2.2'],
                'field-3' => ['value' => 'value 2.3']],
            'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                'field-1' => ['value' => 'value 3.1'],
                'field-2' => ['value' => 'value 3.2'],
                'field-3' => ['value' => ''         ]] ];

        # ─────────────────────────────────────────────────────────────────────
        # view_type = dl
        # ─────────────────────────────────────────────────────────────────────

        $decorator_dl_title = new markup('h3', [], 'view_type = dl');
        $decorator_dl = new decorator('dl');
        $decorator_dl->id = 'demo_dl';
        $decorator_dl->visibility_rowid  = 'visible'; # visible | not_int | hidden
        $decorator_dl->visibility_cellid = 'visible'; # visible | not_int | hidden
        $decorator_dl->data = [
            'rowid-1' => ['attributes' => ['data-row_attribute' => 'value-1', 'class' => ['row_class-1' => 'row_class-1']],
                'field-1' => ['value' => 'value 1.1', 'title' => 'Field #1', 'attributes' => ['data-cell_attribute' => 'value-1.1', 'class' => ['cell_class-1.1' => 'cell_class-1.1']]],
                'field-2' => ['value' => 'value 1.2', 'title' => 'Field #2'],
                'field-3' => ['value' => 'value 1.3', 'title' => 'Field #3']],
            'rowid-2' => ['attributes' => ['data-row_attribute' => 'value-2', 'class' => ['row_class-2' => 'row_class-2']],
                'field-1' => ['value' => 'value 2.1'],
                'field-2' => ['value' => 'value 2.2'],
                'field-3' => ['value' => 'value 2.3']],
            'rowid-3' => ['attributes' => ['data-row_attribute' => 'value-3', 'class' => ['row_class-3' => 'row_class-3']],
                'field-1' => ['value' => 'value 3.1'],
                'field-2' => ['value' => 'value 3.2'],
                'field-3' => ['value' => ''         ]]];

        # ─────────────────────────────────────────────────────────────────────
        # result block
        # ─────────────────────────────────────────────────────────────────────

        return new node([], [
            $decorator_table_title,
            $decorator_table,
            $decorator_table_adaptive_title,
            $decorator_table_adaptive,
            $decorator_table_dl_title,
            $decorator_table_dl,
            $decorator_ul_title,
            $decorator_ul,
            $decorator_dl_title,
            $decorator_dl
        ]);
    }

    ################
    ### messages ###
    ################

    static function block_markup__demo_messages($page, $args = []) {
        $link = (new markup('a', ['href' => '/'], 'Link'))->render();
        message::insert('Credentials', 'credentials');
        message::insert( new text_multiline(['Notice message #%%_number.',              $link], ['number' => 1], ' '), 'notice');
        message::insert( new text_multiline(['Notice message #%%_number.'                    ], ['number' => 2], ' '), 'notice');
        message::insert( new text_multiline(['Notice message #%%_number.'                    ], ['number' => 3], ' '), 'notice');
        message::insert( new text_multiline(['Ok message #%%_number.', 'Default type.', $link], ['number' => 1], ' '));
        message::insert( new text_multiline(['Ok message #%%_number.',                       ], ['number' => 2], ' '));
        message::insert( new text_multiline(['Ok message #%%_number.'                        ], ['number' => 3], ' '));
        message::insert( new text_multiline(['Warning message #%%_number.',             $link], ['number' => 1], ' '), 'warning');
        message::insert( new text_multiline(['Warning message #%%_number.'                   ], ['number' => 2], ' '), 'warning');
        message::insert( new text_multiline(['Warning message #%%_number.'                   ], ['number' => 3], ' '), 'warning');
        message::insert( new text_multiline(['Error message #%%_number!',               $link], ['number' => 1], ' '), 'error');
        message::insert( new text_multiline(['Error message #%%_number!'                     ], ['number' => 2], ' '), 'error');
        message::insert( new text_multiline(['Error message #%%_number!'                     ], ['number' => 3], ' '), 'error');
    }

    ##############
    ### canvas ###
    ##############

    static function block_markup__demo_canvas($page, $args = []) {
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
        return $canvas;
    }

    ################
    ### diagrams ###
    ################

    static function block_markup__demo_diagram_linear($page, $args = []) {
        $diagram = new diagram('Title', 'linear');
        $diagram->slice_insert('Parameter 1', 70, (new text('%%_number sec.', ['number' => locale::format_msecond('0.07')]))->render());
        $diagram->slice_insert('Parameter 2', 20, (new text('%%_number sec.', ['number' => locale::format_msecond('0.02')]))->render());
        $diagram->slice_insert('Parameter 3', 10, (new text('%%_number sec.', ['number' => locale::format_msecond('0.01')]))->render());
        return $diagram;
    }

    static function block_markup__demo_diagram_radial($page, $args = []) {
        $diagram = new diagram('Title', 'radial');
        $diagram->slice_insert('Parameter 1', 40, (new text('%%_number sec.', ['number' => locale::format_msecond('0.04')]))->render(), '#216ce4');
        $diagram->slice_insert('Parameter 2', 30, (new text('%%_number sec.', ['number' => locale::format_msecond('0.03')]))->render(), '#48be38');
        $diagram->slice_insert('Parameter 3', 20, (new text('%%_number sec.', ['number' => locale::format_msecond('0.02')]))->render(), '#fc5740');
        $diagram->slice_insert('Parameter 4', 10, (new text('%%_number sec.', ['number' => locale::format_msecond('0.01')]))->render(), '#fd9a1e');
        return $diagram;
    }

}
