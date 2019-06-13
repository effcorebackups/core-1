<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_page_part extends field_select {

  public $attributes = ['data-type' => 'page_part'];
  public $element_attributes = [
    'name'     => 'page_part',
    'required' => true];
# ─────────────────────────────────────────────────────────────────────
  public $id_area;

  function build() {
    if (!$this->is_builded) {
         $this->is_builded = true;
      parent::build();
      $this->option_insert('- no -', 'not_selected');
      $page_parts = page_part::select_all($this->id_area);
      foreach ($page_parts as $c_row_id => $c_part) {
        $this->option_insert($c_part->managing_title, $c_row_id);
      }
    }
  }

}}