<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class decorator extends node {

  public $data = [];
  public $view_type = 'table'; # table | ul | dl

  function build() {
    $result = new markup('x-decorator', ['data-view-type' => $this->view_type]);
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
          $thead_row->child_insert(
            new table_head_row_cell(['class' => [$c_name => $c_name]],
               $c_info['title']
            ), $c_name
          );
        }
      # make tbody
        foreach ($this->data as $c_rowid => $c_row) {
          $c_tbody_row = new table_body_row(['data-rowid' => $c_rowid]);
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
      # return result
        $result->child_insert(
          new table([], $tbody, $thead), 'result'
        );
        break;

    # ─────────────────────────────────────────────────────────────────────
    # ul (unordered list)
    # ─────────────────────────────────────────────────────────────────────
      case 'ul':
        foreach ($this->data as $c_rowid => $c_row) {
          $c_list = new markup('ul', ['data-rowid' => $c_rowid]);
          foreach ($c_row as $c_name => $c_info) {
            $c_list->child_insert(new markup('li', ['class' => [$c_name => $c_name]], [
              'title' => new markup('x-title', [], $c_info['title']),
              'value' => new markup('x-value', [], $c_info['value'])
            ]), $c_name);
          }
          $result->child_insert(
            $c_list, $c_rowid
          );
        }
        break;

    # ─────────────────────────────────────────────────────────────────────
    # dl (definition list)
    # ─────────────────────────────────────────────────────────────────────
      case 'dl':
        foreach ($this->data as $c_rowid => $c_row) {
          $c_list = new markup('dl', ['data-rowid' => $c_rowid]);
          foreach ($c_row as $c_name => $c_info) {
            $c_list->child_insert(new markup('dt', ['class' => [$c_name => $c_name]], $c_info['title']), 'title-'.$c_name);
            $c_list->child_insert(new markup('dd', ['class' => [$c_name => $c_name]], $c_info['value']), 'value-'.$c_name);
          }
          $result->child_insert(
            $c_list, $c_rowid
          );
        }
        break;

    }
    return $result;
  }

  function render() {
    $markup = $this->build();
    return $markup->render();
  }

}}