<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Decorator extends Markup {

    public $id;
    public $tag_name = 'x-decorator';
    public $attributes = ['data-decorator' => true];
    public $view_type = 'table'; # table | table-adaptive | table-dl | ul | dl | tree | template
    public $template = 'markup_html';
    public $template_item;
    public $mapping = [];
    public $tree_visualization_mode; # null | decorated | decorated-rearrangeable
    public $result_attributes = [];
    public $visibility_row_id  = 'not_int'; # visible | not_int | hidden
    public $visibility_cell_id = 'not_int'; # visible | not_int | hidden
    public $data = [];

    function __construct($view_type = 'table', $attributes = [], $weight = +0) {
        $this->view_type = $view_type;
        parent::__construct(null, $attributes, [], $weight);
    }

    function build() {
        if (!$this->is_builded) {

            $result = new Node;
            $this->attribute_insert('data-view-type'  , $this->view_type);
            $this->attribute_insert('data-id'         , $this->id       );
            $this->attribute_insert('data-items-count', $this->data ? count($this->data) : 0);
            Event::start('on_decorator_build_before', $this->id, ['decorator' => &$this]);

            if ($this->data) {

                foreach ($this->data as $c_row_id => $c_row) {
                    foreach ($c_row as $c_cell_id => $null) {
                        if ($c_cell_id !== 'attributes') {

                            # delete invisible items
                            if (!empty($this->data[$c_row_id][$c_cell_id]['is_not_visible'])) {
                                unset($this->data[$c_row_id][$c_cell_id]);
                                continue;
                            };

                            # apply on_render filter
                            if (!empty($this->data[$c_row_id][$c_cell_id]['converters_on_render'])) {
                                $this->data[$c_row_id][$c_cell_id]['value'] = Entity::converters_apply(
                                    $this->data[$c_row_id][$c_cell_id]['value'],
                                    $this->data[$c_row_id][$c_cell_id]['converters_on_render']
                                );
                            }

                            # apply format
                            if (array_key_exists('format', $this->data[$c_row_id][$c_cell_id])) {
                                if (is_string ($this->data[$c_row_id][$c_cell_id]['value']) ||
                                    is_numeric($this->data[$c_row_id][$c_cell_id]['value']) ||
                                    is_bool   ($this->data[$c_row_id][$c_cell_id]['value'])) {
                                    $type_by_entity = $this->data[$c_row_id][$c_cell_id][ 'type' ] ?? null;
                                    $type_by_format = $this->data[$c_row_id][$c_cell_id]['format'] ?? null;
                                    if ($type_by_format === 'raw'          ) $this->data[$c_row_id][$c_cell_id]['value'] =                                $this->data[$c_row_id][$c_cell_id]['value'];
                                    if ($type_by_format === 'url_from_path') $this->data[$c_row_id][$c_cell_id]['value'] = Core::to_url_from_path((string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'boolean'      ) $this->data[$c_row_id][$c_cell_id]['value'] =    Core::format_logic (  (bool)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'real'         ) $this->data[$c_row_id][$c_cell_id]['value'] =  Locale::format_number( (float)$this->data[$c_row_id][$c_cell_id]['value'], Core::FPART_MAX_LEN);
                                    if ($type_by_format === 'integer'      ) $this->data[$c_row_id][$c_cell_id]['value'] =  Locale::format_number(   (int)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    # datetime → time / datetime → date / datetime → datetime
                                    if ($type_by_format === 'time'     && $type_by_entity !== 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_time              ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'time'     && $type_by_entity === 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_time_from_datetime( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'date'     && $type_by_entity !== 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_date              ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'date'     && $type_by_entity === 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_date_from_datetime( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'time_utc' && $type_by_entity !== 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_utc_time              ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'time_utc' && $type_by_entity === 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_utc_time_from_datetime( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'date_utc' && $type_by_entity !== 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_utc_date              ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'date_utc' && $type_by_entity === 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_utc_date_from_datetime( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'datetime'                                  ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_datetime          ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === 'datetime_utc'                              ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_utc_datetime          ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    # default behavior: type by entity if format = null
                                    if ($type_by_format === null && $type_by_entity === 'boolean' ) $this->data[$c_row_id][$c_cell_id]['value'] =   Core::format_logic       (   (bool)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === null && $type_by_entity === 'real'    ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_number      (  (float)$this->data[$c_row_id][$c_cell_id]['value'], Core::FPART_MAX_LEN);
                                    if ($type_by_format === null && $type_by_entity === 'integer' ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_number      (    (int)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === null && $type_by_entity === 'time'    ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_time    ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === null && $type_by_entity === 'date'    ) $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_date    ( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                    if ($type_by_format === null && $type_by_entity === 'datetime') $this->data[$c_row_id][$c_cell_id]['value'] = Locale::format_loc_datetime( (string)$this->data[$c_row_id][$c_cell_id]['value'] );
                                }
                            }

                            # convert scalar value to '\effcore\Text' and apply translation and tokens
                            if (is_string ($this->data[$c_row_id][$c_cell_id]['value']) ||
                                is_numeric($this->data[$c_row_id][$c_cell_id]['value'])) {
                                $this->data[$c_row_id][$c_cell_id]['value'] = new Text(
                                    (string)$this->data[$c_row_id][$c_cell_id]['value'], [], true, false
                                );
                            }
                            if ($this->data[$c_row_id][$c_cell_id]['value'] instanceof Text && array_key_exists('is_apply_translation', $this->data[$c_row_id][$c_cell_id])) $this->data[$c_row_id][$c_cell_id]['value']->is_apply_translation = $this->data[$c_row_id][$c_cell_id]['is_apply_translation'];
                            if ($this->data[$c_row_id][$c_cell_id]['value'] instanceof Text && array_key_exists('is_apply_tokens'     , $this->data[$c_row_id][$c_cell_id])) $this->data[$c_row_id][$c_cell_id]['value']->is_apply_tokens      = $this->data[$c_row_id][$c_cell_id]['is_apply_tokens'     ];
                        }
                    }
                }

                switch ($this->view_type) {

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = table
                    # ─────────────────────────────────────────────────────────────────────

                    case 'table':
                        $thead     = new Markup_Table_head;
                        $thead_row = new Markup_Table_head_row;
                        $tbody     = new Markup_Table_body;
                        $thead->child_insert($thead_row, 'head_row_main');
                        # make thead
                        foreach (reset($this->data) as $c_cell_id => $c_cell) {
                            $c_cell_attributes = [];
                            if ($this->visibility_row_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                            if ($c_cell_id !== 'attributes') {
                                $thead_row->child_insert(
                                    new Markup_Table_head_row_cell($c_cell_attributes, $this->data[key($this->data)][$c_cell_id]['title']), $c_cell_id
                                );
                            }
                        }
                        # make tbody
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_row_attributes = static::attributes_eject($c_row);
                            if ($this->visibility_row_id === 'visible'                      ) $c_row_attributes['data-row-id'] = $c_row_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-row-id'] = $c_row_id;
                            $c_tbody_row = new Markup_Table_body_row($c_row_attributes);
                            foreach ($c_row as $c_cell_id => $c_cell) {
                                $c_cell_attributes = static::attributes_eject($c_cell);
                                if ($this->visibility_cell_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                if ($this->visibility_cell_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                $c_tbody_row->child_insert(
                                    new Markup_Table_body_row_cell($c_cell_attributes, $this->data[$c_row_id][$c_cell_id]['value']), $c_cell_id
                                );
                            }
                            $tbody->child_insert(
                                $c_tbody_row, $c_row_id
                            );
                        }
                        # make result
                        if ($thead_row->children_select_count() === 0) {
                            $thead->child_delete('head_row_main');
                        }
                        $result->child_insert(
                            new Markup_Table($this->attributes_select('result_attributes'), $tbody, $thead), 'table'
                        );
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = table-adaptive
                    # ─────────────────────────────────────────────────────────────────────

                    case 'table-adaptive':
                        $titles    = [];
                        $xhead     = new Markup('x-head');
                        $xhead_row = new Markup('x-row' );
                        $xbody     = new Markup('x-body');
                        $xhead->child_insert($xhead_row, 'head_row_main');
                        # make xhead
                        foreach (reset($this->data) as $c_cell_id => $c_cell) {
                            $c_cell_attributes = [];
                            if ($this->visibility_row_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                            if ($c_cell_id !== 'attributes') {
                                $titles[$c_cell_id] = $this->data[key($this->data)][$c_cell_id]['title'];
                                $xhead_row->child_insert(
                                    new Markup('x-cell', $c_cell_attributes, $titles[$c_cell_id]), $c_cell_id
                                );
                            }
                        }
                        # make xbody
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_row_attributes = static::attributes_eject($c_row);
                            if ($this->visibility_row_id === 'visible'                      ) $c_row_attributes['data-row-id'] = $c_row_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-row-id'] = $c_row_id;
                            $c_xbody_row = new Markup('x-row', $c_row_attributes);
                            foreach ($c_row as $c_cell_id => $c_cell) {
                                $c_cell_attributes = static::attributes_eject($c_cell);
                                if ($this->visibility_cell_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                if ($this->visibility_cell_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                $c_xbody_row->child_insert(
                                    new Markup('x-cell', $c_cell_attributes, [
                                        new Markup('x-title', [], $this->data[$c_row_id][$c_cell_id]['title'] ?? $titles[$c_cell_id]),
                                        new Markup('x-value', [], $this->data[$c_row_id][$c_cell_id]['value']                       )
                                    ]), $c_cell_id
                                );
                            }
                            $xbody->child_insert(
                                $c_xbody_row, $c_row_id
                            );
                        }
                        # make result
                        $result->child_insert(
                            new Markup('x-table', $this->attributes_select('result_attributes'), [$xhead, $xbody]), 'x_table'
                        );
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = table-dl
                    # ─────────────────────────────────────────────────────────────────────

                    case 'table-dl':
                        $titles = [];
                        foreach (reset($this->data) as $c_cell_id => $c_cell)
                            if ($c_cell_id !== 'attributes')
                                $titles[$c_cell_id] = $this->data[key($this->data)][$c_cell_id]['title'];
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row) + ['data-view-type' => 'table-dl'];
                            if ($this->visibility_row_id === 'visible'                      ) $c_row_attributes['data-row-id'] = $c_row_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-row-id'] = $c_row_id;
                            $c_table = new Markup('x-table', $c_row_attributes);
                            foreach ($c_row as $c_cell_id => $c_cell) {
                                $c_cell_attributes = static::attributes_eject($c_cell);
                                if ($this->visibility_cell_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                if ($this->visibility_cell_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                $c_table->child_insert(new Markup('x-row', $c_cell_attributes, [
                                    'title' => new Markup('x-cell', ['data-role' => 'title'], $this->data[$c_row_id][$c_cell_id]['title'] ?? $titles[$c_cell_id]),
                                    'value' => new Markup('x-cell', ['data-role' => 'value'], $this->data[$c_row_id][$c_cell_id]['value']                       )
                                ]), $c_cell_id);
                            }
                            $result->child_insert(
                                $c_table, $c_row_id
                            );
                        }
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = ul
                    # ─────────────────────────────────────────────────────────────────────

                    case 'ul':
                        $titles = [];
                        foreach (reset($this->data) as $c_cell_id => $c_cell)
                            if ($c_cell_id !== 'attributes')
                                $titles[$c_cell_id] = $this->data[key($this->data)][$c_cell_id]['title'];
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row);
                            if ($this->visibility_row_id === 'visible'                      ) $c_row_attributes['data-row-id'] = $c_row_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-row-id'] = $c_row_id;
                            $c_list = new Markup('ul', $c_row_attributes);
                            foreach ($c_row as $c_cell_id => $c_cell) {
                                $c_cell_attributes = static::attributes_eject($c_cell);
                                if ($this->visibility_cell_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                if ($this->visibility_cell_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                $c_list->child_insert(new Markup('li', $c_cell_attributes, [
                                    'title' => new Markup('x-title', [], $this->data[$c_row_id][$c_cell_id]['title'] ?? $titles[$c_cell_id]),
                                    'value' => new Markup('x-value', [], $this->data[$c_row_id][$c_cell_id]['value']                       )
                                ]), $c_cell_id);
                            }
                            $result->child_insert(
                                $c_list, $c_row_id
                            );
                        }
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = dl
                    # ─────────────────────────────────────────────────────────────────────

                    case 'dl':
                        $titles = [];
                        foreach (reset($this->data) as $c_cell_id => $c_cell)
                            if ($c_cell_id !== 'attributes')
                                $titles[$c_cell_id] = $this->data[key($this->data)][$c_cell_id]['title'];
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row);
                            if ($this->visibility_row_id === 'visible'                      ) $c_row_attributes['data-row-id'] = $c_row_id;
                            if ($this->visibility_row_id === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-row-id'] = $c_row_id;
                            $c_list = new Markup('dl', $c_row_attributes);
                            foreach ($c_row as $c_cell_id => $c_cell) {
                                $c_cell_attributes = static::attributes_eject($c_cell);
                                if ($this->visibility_cell_id === 'visible'                       ) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                if ($this->visibility_cell_id === 'not_int' && !is_int($c_cell_id)) $c_cell_attributes['data-cell-id'] = $c_cell_id;
                                $c_list->child_insert(new Markup('dt', $c_cell_attributes, $this->data[$c_row_id][$c_cell_id]['title'] ?? $titles[$c_cell_id]), 'title-'.$c_cell_id);
                                $c_list->child_insert(new Markup('dd', $c_cell_attributes, $this->data[$c_row_id][$c_cell_id]['value']                       ), 'value-'.$c_cell_id);
                            }
                            $result->child_insert(
                                $c_list, $c_row_id
                            );
                        }
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = tree
                    # ─────────────────────────────────────────────────────────────────────

                    case 'tree':
                        $trees = new Node;
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_id        = Core::to_rendered( array_key_exists('id'       , $c_row) ? $c_row['id'       ]['value'] : (array_key_exists('id'       , $this->mapping) && array_key_exists($this->mapping['id'       ], $c_row) ? $c_row[$this->mapping['id'       ]]['value'] : null) );
                            $c_id_parent = Core::to_rendered( array_key_exists('id_parent', $c_row) ? $c_row['id_parent']['value'] : (array_key_exists('id_parent', $this->mapping) && array_key_exists($this->mapping['id_parent'], $c_row) ? $c_row[$this->mapping['id_parent']]['value'] : null) );
                            $c_id_tree   = Core::to_rendered( array_key_exists('id_tree'  , $c_row) ? $c_row['id_tree'  ]['value'] : (array_key_exists('id_tree'  , $this->mapping) && array_key_exists($this->mapping['id_tree'  ], $c_row) ? $c_row[$this->mapping['id_tree'  ]]['value'] : null) );
                            $c_title     = Core::to_rendered( array_key_exists('title'    , $c_row) ? $c_row['title'    ]['value'] : (array_key_exists('title'    , $this->mapping) && array_key_exists($this->mapping['title'    ], $c_row) ? $c_row[$this->mapping['title'    ]]['value'] : null) );
                            $c_url       = Core::to_rendered( array_key_exists('url'      , $c_row) ? $c_row['url'      ]['value'] : (array_key_exists('url'      , $this->mapping) && array_key_exists($this->mapping['url'      ], $c_row) ? $c_row[$this->mapping['url'      ]]['value'] : null) );
                            $c_weight    = Core::to_rendered( array_key_exists('weight'   , $c_row) ? $c_row['weight'   ]['value'] : (array_key_exists('weight'   , $this->mapping) && array_key_exists($this->mapping['weight'   ], $c_row) ? $c_row[$this->mapping['weight'   ]]['value'] : null) );
                            $c_access    =                    array_key_exists('access'   , $c_row) ? $c_row['access'   ]['value'] : (array_key_exists('access'   , $this->mapping) && array_key_exists($this->mapping['access'   ], $c_row) ? $c_row[$this->mapping['access'   ]]['value'] : null);
                            $c_extra     =                    array_key_exists('extra'    , $c_row) ? $c_row['extra'    ]['value'] : (array_key_exists('extra'    , $this->mapping) && array_key_exists($this->mapping['extra'    ], $c_row) ? $c_row[$this->mapping['extra'    ]]['value'] : null);
                            $c_id_tree = 'decorator-'.$c_id_tree;
                            $c_tree = Tree::insert($this->description ?? null, $c_id_tree, null, [], 0, 'page');
                            $c_tree->visualization_mode = $this->tree_visualization_mode;
                            if ($trees->child_select(         $c_id_tree) === null)
                                $trees->child_insert($c_tree, $c_id_tree);
                            $c_tree_item = Tree_item::insert($c_title,
                                $c_id_tree.'-'.$c_id, $c_id_parent !== null ?
                                $c_id_tree.'-'.$c_id_parent : null,
                                $c_id_tree,    $c_url, $c_access, ['data-real-id' => $c_id], [], $c_weight, 'page');
                            $c_tree_item->extra = $c_extra;
                        }
                        $result->child_insert(
                            $trees, 'trees'
                        );
                        break;

                    # ─────────────────────────────────────────────────────────────────────
                    # view_type = template(code|file|node|text)
                    # ─────────────────────────────────────────────────────────────────────

                    case 'template':
                        foreach ($this->data as $c_row_id => $c_row) {
                            $c_template_name_original = $this->template_item;
                            $c_template_name = Template::pick_name($c_template_name_original);
                            if ($c_template_name) {
                                $c_template = Template::make_new($c_template_name);
                                foreach ($this->mapping as $c_arg_name => $c_cell_name) {
                                    if (isset($c_row[$c_cell_name]) && is_array($c_row[$c_cell_name])) {
                                        $c_template->arg_set(
                                            $c_arg_name,
                                            Core::to_rendered($c_row[$c_cell_name]['value']),
                                                              $c_row[$c_cell_name]['value']
                                        );
                                    }
                                }
                                $result->child_insert(
                                    new Text($c_template->render()), $c_row_id
                                );
                            } else {
                                $result->child_insert(
                                    new Text('TEMPLATE "%%_name" WAS NOT FOUND!', ['name' => $c_template_name_original]), $c_row_id
                                );
                            }
                        }
                        break;

                }
                $this->child_insert(
                    $result, 'result'
                );
            } else {
                $this->child_insert(
                    new Markup('x-no-items', ['data-style' => 'table'], 'No items.'), 'message_no_items'
                );
            }
            Event::start('on_decorator_build_after', $this->id, ['decorator' => &$this]);
            $this->is_builded = true;
            return $this;

        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

    static function attributes_eject(&$row) {
        if (isset($row['attributes'])) {
            $attributes = $row['attributes'];
                    unset($row['attributes']);
            return $attributes;
        } return [];
    }

}
