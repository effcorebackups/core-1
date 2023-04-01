<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class decorator extends markup {

  public $id;
  public $tag_name = 'x-decorator';
  public $attributes = ['data-decorator' => true];
  public $view_type = 'table'; # table | table-adaptive | table-dl | ul | dl | tree | template
  public $template = 'markup_html';
  public $template_item;
  public $mapping = [];
  public $tree_visualization_mode; # null | decorated | decorated-rearrangeable
  public $result_attributes = [];
  public $visibility_rowid  = 'not_int'; # visible | not_int | hidden
  public $visibility_cellid = 'not_int'; # visible | not_int | hidden
  public $data = [];

  function __construct($view_type = 'table', $attributes = [], $weight = 0) {
    $this->view_type = $view_type;
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {

      $result = new node;
      $this->attribute_insert('data-view-type', $this->view_type);
      $this->attribute_insert('data-id',        $this->id       );
      event::start('on_decorator_build_before', $this->id, ['decorator' => &$this]);

      if ($this->data) {
        switch ($this->view_type) {

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table
        # ─────────────────────────────────────────────────────────────────────
          case 'table':
            $thead     = new table_head;
            $thead_row = new table_head_row;
            $tbody     = new table_body;
            $thead->child_insert($thead_row, 'head_row_main');
          # make thead
            foreach (reset($this->data) as $c_name => $c_info) {
              if (true                                                     ) $c_cell_attributes = [];
              if ($this->visibility_rowid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
              if ($c_name !== 'attributes') {
                $thead_row->child_insert(
                  new table_head_row_cell($c_cell_attributes, $c_info['title']), $c_name
                );
              }
            }
          # make tbody
            foreach ($this->data as $c_row_id => $c_row) {
              if (true                                                       ) $c_row_attributes = static::attributes_eject($c_row);
              if ($this->visibility_rowid === 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
              $c_tbody_row = new table_body_row($c_row_attributes);
              foreach ($c_row as $c_name => $c_info) {
                if (true                                                      ) $c_cell_attributes = static::attributes_eject($c_info);
                if ($this->visibility_cellid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
                if ($this->visibility_cellid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
                $c_tbody_row->child_insert(
                  new table_body_row_cell($c_cell_attributes, $c_info['value']), $c_name
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
              new table($this->attributes_select('result_attributes'), $tbody, $thead), 'table'
            );
            break;

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table-adaptive
        # ─────────────────────────────────────────────────────────────────────
          case 'table-adaptive':
            $titles    = [];
            $xhead     = new markup('x-head');
            $xhead_row = new markup('x-row' );
            $xbody     = new markup('x-body');
            $xhead->child_insert($xhead_row, 'head_row_main');
          # make xhead
            foreach (reset($this->data) as $c_name => $c_info) {
              if (true                                                     ) $c_cell_attributes = [];
              if ($this->visibility_rowid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
              if ($c_name !== 'attributes') {
                $titles[$c_name] = $c_info['title'];
                $xhead_row->child_insert(
                  new markup('x-cell', $c_cell_attributes, $c_info['title']), $c_name
                );
              }
            }
          # make xbody
            foreach ($this->data as $c_row_id => $c_row) {
              if (true                                                       ) $c_row_attributes = static::attributes_eject($c_row);
              if ($this->visibility_rowid === 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
              $c_xbody_row = new markup('x-row', $c_row_attributes);
              foreach ($c_row as $c_name => $c_info) {
                if (true                                                      ) $c_cell_attributes = static::attributes_eject($c_info);
                if ($this->visibility_cellid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
                if ($this->visibility_cellid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
                $c_xbody_row->child_insert(
                  new markup('x-cell', $c_cell_attributes, [
                    new markup('x-title', [], $c_info['title'] ?? $titles[$c_name]),
                    new markup('x-value', [], $c_info['value']                    )
                  ]), $c_name
                );
              }
              $xbody->child_insert(
                $c_xbody_row, $c_row_id
              );
            }
          # make result
            $result->child_insert(
              new markup('x-table', $this->attributes_select('result_attributes'), [$xhead, $xbody]), 'x_table'
            );
            break;

        # ─────────────────────────────────────────────────────────────────────
        # view_type = table-dl
        # ─────────────────────────────────────────────────────────────────────
          case 'table-dl':
            $titles = [];
            foreach (reset($this->data) as $c_name => $c_info)
              if ($c_name !== 'attributes')
                $titles[$c_name] = $c_info['title'];
            foreach ($this->data as $c_row_id => $c_row) {
              if (true                                                       ) $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row) + ['data-view-type' => 'table-dl'];
              if ($this->visibility_rowid === 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
              $c_table = new markup('x-table', $c_row_attributes);
              foreach ($c_row as $c_name => $c_info) {
                if (true                                                      ) $c_cell_attributes = static::attributes_eject($c_info);
                if ($this->visibility_cellid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
                if ($this->visibility_cellid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
                $c_table->child_insert(new markup('x-row', $c_cell_attributes, [
                  'title' => new markup('x-cell', ['data-role' => 'title'], $c_info['title'] ?? $titles[$c_name]),
                  'value' => new markup('x-cell', ['data-role' => 'value'], $c_info['value']                    )
                ]), $c_name);
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
            foreach (reset($this->data) as $c_name => $c_info)
              if ($c_name !== 'attributes')
                $titles[$c_name] = $c_info['title'];
            foreach ($this->data as $c_row_id => $c_row) {
              if (true                                                       ) $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row);
              if ($this->visibility_rowid === 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
              $c_list = new markup('ul', $c_row_attributes);
              foreach ($c_row as $c_name => $c_info) {
                if (true                                                      ) $c_cell_attributes = static::attributes_eject($c_info);
                if ($this->visibility_cellid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
                if ($this->visibility_cellid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
                $c_list->child_insert(new markup('li', $c_cell_attributes, [
                  'title' => new markup('x-title', [], $c_info['title'] ?? $titles[$c_name]),
                  'value' => new markup('x-value', [], $c_info['value']                    )
                ]), $c_name);
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
            foreach (reset($this->data) as $c_name => $c_info)
              if ($c_name !== 'attributes')
                $titles[$c_name] = $c_info['title'];
            foreach ($this->data as $c_row_id => $c_row) {
              if (true                                                       ) $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_eject($c_row);
              if ($this->visibility_rowid === 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
              if ($this->visibility_rowid === 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
              $c_list = new markup('dl', $c_row_attributes);
              foreach ($c_row as $c_name => $c_info) {
                if (true                                                      ) $c_cell_attributes = static::attributes_eject($c_info);
                if ($this->visibility_cellid === 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
                if ($this->visibility_cellid === 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
                $c_list->child_insert(new markup('dt', $c_cell_attributes, $c_info['title'] ?? $titles[$c_name]), 'title-'.$c_name);
                $c_list->child_insert(new markup('dd', $c_cell_attributes, $c_info['value']                    ), 'value-'.$c_name);
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
            $trees = new node;
            foreach ($this->data as $c_row_id => $c_row) {
              $c_id        = core::return_rendered( array_key_exists('id',        $c_row) ? $c_row['id'       ]['value'] : (array_key_exists('id',        $this->mapping) && array_key_exists($this->mapping['id'       ], $c_row) ? $c_row[$this->mapping['id'       ]]['value'] : null) );
              $c_id_parent = core::return_rendered( array_key_exists('id_parent', $c_row) ? $c_row['id_parent']['value'] : (array_key_exists('id_parent', $this->mapping) && array_key_exists($this->mapping['id_parent'], $c_row) ? $c_row[$this->mapping['id_parent']]['value'] : null) );
              $c_id_tree   = core::return_rendered( array_key_exists('id_tree',   $c_row) ? $c_row['id_tree'  ]['value'] : (array_key_exists('id_tree',   $this->mapping) && array_key_exists($this->mapping['id_tree'  ], $c_row) ? $c_row[$this->mapping['id_tree'  ]]['value'] : null) );
              $c_title     = core::return_rendered( array_key_exists('title',     $c_row) ? $c_row['title'    ]['value'] : (array_key_exists('title',     $this->mapping) && array_key_exists($this->mapping['title'    ], $c_row) ? $c_row[$this->mapping['title'    ]]['value'] : null) );
              $c_url       = core::return_rendered( array_key_exists('url',       $c_row) ? $c_row['url'      ]['value'] : (array_key_exists('url',       $this->mapping) && array_key_exists($this->mapping['url'      ], $c_row) ? $c_row[$this->mapping['url'      ]]['value'] : null) );
              $c_weight    = core::return_rendered( array_key_exists('weight',    $c_row) ? $c_row['weight'   ]['value'] : (array_key_exists('weight',    $this->mapping) && array_key_exists($this->mapping['weight'   ], $c_row) ? $c_row[$this->mapping['weight'   ]]['value'] : null) );
              $c_access    =                        array_key_exists('access',    $c_row) ? $c_row['access'   ]['value'] : (array_key_exists('access',    $this->mapping) && array_key_exists($this->mapping['access'   ], $c_row) ? $c_row[$this->mapping['access'   ]]['value'] : null);
              $c_extra     =                        array_key_exists('extra',     $c_row) ? $c_row['extra'    ]['value'] : (array_key_exists('extra',     $this->mapping) && array_key_exists($this->mapping['extra'    ], $c_row) ? $c_row[$this->mapping['extra'    ]]['value'] : null);
              $c_id_tree = 'decorator-'.$c_id_tree;
              $c_tree = tree::insert($this->title ?? null, $c_id_tree, null, [], 0, 'page');
              $c_tree->visualization_mode = $this->tree_visualization_mode;
              if ($trees->child_select(         $c_id_tree) === null)
                  $trees->child_insert($c_tree, $c_id_tree);
              $c_tree_item = tree_item::insert($c_title,
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
        # view_type = template
        # ─────────────────────────────────────────────────────────────────────
          case 'template':
            foreach ($this->data as $c_row_id => $c_row) {
              $c_template = template::make_new(template::pick_name($this->template_item));
              foreach ($this->mapping as $c_arg_name => $c_cell_name) {
                if (isset($c_row[$c_cell_name]) && is_array($c_row[$c_cell_name])) {
                  $c_template->arg_set($c_arg_name,
                    core::return_rendered($c_row[$c_cell_name]['value'])
                  );
                }
              }
              $result->child_insert(
                new text($c_template->render()), $c_row_id
              );
            }
            break;

        }
        $this->child_insert(
          $result, 'result'
        );
      } else {
        $this->child_insert(
          new markup('x-no-items', ['data-style' => 'table'], 'No items.'), 'message_no_items'
        );
      }
      event::start('on_decorator_build_after', $this->id, ['decorator' => &$this]);
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

}}