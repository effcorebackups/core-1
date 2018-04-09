<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form_field_select extends form_field {

  public $select_attributes = [];
  public $values = [];
  public $selected = [];
  public $disabled = [];

  function build() {
    $this->child_insert(new markup('select', $this->attribute_select('', 'select_attributes')), 'element');
    $this->child_select('element')->title = $this->title;
    foreach ($this->values as $c_id => $c_data) {
      if (is_object($c_data) && !empty($c_data->title) && !empty($c_data->values)) {
        if (!$this->optgroup_select($c_id))
             $this->optgroup_insert($c_id, $c_data->title);
        foreach ($c_data->values as $g_id => $g_data) {
          $this->option_insert($g_data, $g_id, [], $c_id);
        }
      } else {
        $this->option_insert($c_data, $c_id);
      }
    }
  }

  function optgroup_select($id) {
    return $this->child_select('element')->child_select($id);
  }

  function optgroup_insert($id, $title, $attr = []) {
    $this->child_select('element')->child_insert(
      new markup('optgroup', $attr + ['label' => $title]), $id
    );
  }

  function option_insert($title, $value, $attr = [], $grp_id = null) {
    $parent_el = $grp_id ? $this->child_select('element')->child_select($grp_id) :
                           $this->child_select('element');
    $new_option = new markup('option', $attr, ['content' => $title]);
    $new_option->attribute_insert('value', $value === 'not_selected' ? '' : $value);
    if (isset($this->selected[$value])) $new_option->attribute_insert('selected', 'selected');
    if (isset($this->disabled[$value])) $new_option->attribute_insert('disabled', 'disabled');
    $parent_el->child_insert($new_option, $value);
  }

}}