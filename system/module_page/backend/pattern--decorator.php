<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class decorator extends node {

  public $data = [];
  public $view_type = 'table';

  function build() {
    $result = new markup('x-items', ['data-view-type' => $this->view_type]);
    switch ($this->view_type) {

    # ─────────────────────────────────────────────────────────────────────
    # table
    # ─────────────────────────────────────────────────────────────────────
      case 'table':
        $thead     = new table_head    ();
        $thead_row = new table_head_row();
        $tbody     = new table_body    ();
        $thead->child_insert($thead_row);
      # make thead
        foreach (reset($this->data) as $c_name => $c_info) {
          $thead_row->child_insert(
            new table_head_row_cell(['class' => [$c_name => $c_name]],
              $c_info['title']
            )
          );
        }
      # make tbody
        foreach ($this->data as $c_rowid => $c_row) {
          $c_tbody_row = new table_body_row(['data-rowid' => $c_rowid]);
          foreach ($c_row as $c_name => $c_info) {
            $c_tbody_row->child_insert(
              new table_body_row_cell(['class' => [$c_name => $c_name]],
                $c_info['value']
              )
            );
          }
          $tbody->child_insert(
            $c_tbody_row
          );
        }
      # return result
        $table = new table([], $tbody, $thead);
        $result->child_insert(
          $table
        );
        break;

    # ─────────────────────────────────────────────────────────────────────
    # list
    # ─────────────────────────────────────────────────────────────────────
      case 'list':
        foreach ($this->data as $c_rowid => $c_row) {
          $c_list = new markup('ul', ['data-rowid' => $c_rowid]);
          foreach ($c_row as $c_name => $c_info) {
            $c_list->child_insert(new markup('li', ['class' => [$c_name => $c_name]], [
              new markup('x-title', [], $c_info['title']),
              new markup('x-value', [], $c_info['value'])
            ]), $c_name);
          }
          $result->child_insert(
            $c_list
          );
        }
        break;

    }
    return $result;
  }

}}