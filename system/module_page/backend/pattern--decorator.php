<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class decorator extends markup {

  public $tag_name = 'x-decorator';
  public $view_type = 'table'; # table | ul | dl
  public $result_attributes = [];
  public $is_skip_rowid_int_class = true;
  public $data = [];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, $attributes, [], $weight);
  }

  function build() {
    $this->children_delete_all();
    $this->attribute_insert('data-view-type', $this->view_type);
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
          if ($c_name != 'attributes') {
            $thead_row->child_insert(
              new table_head_row_cell(['class' => [$c_name => $c_name]],
                 $c_info['title']
              ), $c_name
            );
          }
        }
      # make tbody
        foreach ($this->data as $c_rowid => $c_row) {
          $c_tbody_row = new table_body_row(static::attributes_shift($c_row));
          if (!(is_int($c_rowid) && $this->is_skip_rowid_int_class)) $c_tbody_row->attribute_insert('data-rowid', $c_rowid);
          foreach ($c_row as $c_name => $c_info) {
            $c_tbody_row->child_insert(
              new table_body_row_cell(['class' => [$c_name => $c_name]],
                 $c_info['value']
              ), $c_name
            );
          }
          $tbody->child_insert(
            $c_tbody_row, $c_rowid
          );
        }
      # make result
        $this->child_insert(
          new table($this->attributes_select('result_attributes'), $tbody, $thead), 'result'
        );
        break;

    # ─────────────────────────────────────────────────────────────────────
    # ul (unordered list)
    # ─────────────────────────────────────────────────────────────────────
      case 'ul':
        foreach ($this->data as $c_rowid => $c_row) {
          $c_list = new markup('ul', $this->attributes_select('result_attributes'));
          if (!(is_int($c_rowid) && $this->is_skip_rowid_int_class)) $c_list->attribute_insert('data-rowid', $c_rowid);
          foreach ($c_row as $c_name => $c_info) {
            $c_list->child_insert(new markup('li', ['class' => [$c_name => $c_name]], [
              'title' => new markup('x-title', [], $c_info['title']),
              'value' => new markup('x-value', [], $c_info['value'])
            ]), $c_name);
          }
          $this->child_insert(
            $c_list, $c_rowid
          );
        }
        break;

    # ─────────────────────────────────────────────────────────────────────
    # dl (definition list)
    # ─────────────────────────────────────────────────────────────────────
      case 'dl':
        foreach ($this->data as $c_rowid => $c_row) {
          $c_list = new markup('dl', $this->attributes_select('result_attributes'));
          if (!(is_int($c_rowid) && $this->is_skip_rowid_int_class)) $c_list->attribute_insert('data-rowid', $c_rowid);
          foreach ($c_row as $c_name => $c_info) {
            $c_list->child_insert(new markup('dt', ['class' => [$c_name => $c_name]], $c_info['title']), 'title-'.$c_name);
            $c_list->child_insert(new markup('dd', ['class' => [$c_name => $c_name]], $c_info['value']), 'value-'.$c_name);
          }
          $this->child_insert(
            $c_list, $c_rowid
          );
        }
        break;

    }
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