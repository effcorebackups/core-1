<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class decorator extends markup {

  public $id;
  public $tag_name = 'x-decorator';
  public $view_type = 'table'; # table | ul | dl | tree
  public $result_attributes = [];
  public $visibility_rowid  = 'not_int'; # visible | not_int | hidden
  public $visibility_cellid = 'not_int'; # visible | not_int | hidden
  public $data = [];

  function __construct($view_type = 'table', $attributes = [], $weight = 0) {
    $this->view_type = $view_type;
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    $result = new node();
    $this->children_delete_all();
    $this->attribute_insert('data-view-type', $this->view_type);
    event::start('on_decorator_before_build', $this->id, [&$this]);

    if ($this->data) {
      switch ($this->view_type) {

      # ─────────────────────────────────────────────────────────────────────
      # table
      # ─────────────────────────────────────────────────────────────────────
        case 'table':
          $thead     = new table_head    ();
          $thead_row = new table_head_row();
          $tbody     = new table_body    ();
          $thead->child_insert($thead_row, 'head_row_main');
        # make thead
          foreach (reset($this->data) as $c_name => $c_info) {
            $c_cell_attributes = [];
            if ($this->visibility_rowid == 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
            if ($this->visibility_rowid == 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
            if ($c_name != 'attributes') {
              $thead_row->child_insert(
                new table_head_row_cell($c_cell_attributes, $c_info['title']), $c_name
              );
            }
          }
        # make tbody
          foreach ($this->data as $c_row_id => $c_row) {
            $c_row_attributes = static::attributes_shift($c_row);
            if ($this->visibility_rowid == 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
            if ($this->visibility_rowid == 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
            $c_tbody_row = new table_body_row($c_row_attributes);
            foreach ($c_row as $c_name => $c_info) {
              $c_cell_attributes = static::attributes_shift($c_info);
              if ($this->visibility_cellid == 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
              if ($this->visibility_cellid == 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
              $c_tbody_row->child_insert(
                new table_body_row_cell($c_cell_attributes, $c_info['value']), $c_name
              );
            }
            $tbody->child_insert(
              $c_tbody_row, $c_row_id
            );
          }
        # make result
          $result->child_insert(
            new table($this->attributes_select('result_attributes'), $tbody, $thead)
          );
          break;

      # ─────────────────────────────────────────────────────────────────────
      # ul (unordered list)
      # ─────────────────────────────────────────────────────────────────────
        case 'ul':
          foreach ($this->data as $c_row_id => $c_row) {
            $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_shift($c_row);
            if ($this->visibility_rowid == 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
            if ($this->visibility_rowid == 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
            $c_list = new markup('ul', $c_row_attributes);
            foreach ($c_row as $c_name => $c_info) {
              $c_cell_attributes = static::attributes_shift($c_info);
              if ($this->visibility_cellid == 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
              if ($this->visibility_cellid == 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
              $c_list->child_insert(new markup('li', $c_cell_attributes, [
                'title' => new markup('x-title', [], $c_info['title']),
                'value' => new markup('x-value', [], $c_info['value'])
              ]), $c_name);
            }
            $result->child_insert(
              $c_list, $c_row_id
            );
          }
          break;

      # ─────────────────────────────────────────────────────────────────────
      # dl (definition list)
      # ─────────────────────────────────────────────────────────────────────
        case 'dl':
          foreach ($this->data as $c_row_id => $c_row) {
            $c_row_attributes = $this->attributes_select('result_attributes') + static::attributes_shift($c_row);
            if ($this->visibility_rowid == 'visible'                      ) $c_row_attributes['data-rowid'] = $c_row_id;
            if ($this->visibility_rowid == 'not_int' && !is_int($c_row_id)) $c_row_attributes['data-rowid'] = $c_row_id;
            $c_list = new markup('dl', $c_row_attributes);
            foreach ($c_row as $c_name => $c_info) {
              $c_cell_attributes = static::attributes_shift($c_info);
              if ($this->visibility_cellid == 'visible'                    ) $c_cell_attributes['data-cellid'] = $c_name;
              if ($this->visibility_cellid == 'not_int' && !is_int($c_name)) $c_cell_attributes['data-cellid'] = $c_name;
              $c_list->child_insert(new markup('dt', $c_cell_attributes, $c_info['title']), 'title-'.$c_name);
              $c_list->child_insert(new markup('dd', $c_cell_attributes, $c_info['value']), 'value-'.$c_name);
            }
            $result->child_insert(
              $c_list, $c_row_id
            );
          }
          break;

      # ─────────────────────────────────────────────────────────────────────
      # tree
      # ─────────────────────────────────────────────────────────────────────
        case 'tree':
          foreach ($this->data as $c_row_id => $c_row) {
            tree::item_insert(
              $c_row['cell-title'    ]['value'],
              $c_row['cell-id'       ]['value'],
              $c_row['cell-id_parent']['value'] ?: 'M:'.$c_row['cell-id_tree']['value'],
              $c_row['cell-url'      ]['value']
            );
          }
          $result->child_insert(
            tree::select(reset($this->data)['cell-id_tree']['value'])
          );
          break;

      }
      $this->child_insert(
        $result, 'result'
      );
    } else {
      $this->child_insert(
        new markup('x-no-result', [], 'no items'), 'no_result'
      );
    }
    event::start('on_decorator_after_build', $this->id, [&$this]);
    return $this;
  }

  function render() {
    $this->build();
    return parent::render();
  }

  static function attributes_shift(&$row) {
    if (isset($row['attributes'])) {
      $attributes = $row['attributes'];
              unset($row['attributes']);
      return $attributes;
    } else return [];
  }

}}