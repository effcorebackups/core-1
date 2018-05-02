<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select extends field {

  public $attributes = ['x-type' => 'select'];
  public $element_class = '\\effcore\\markup';
  public $element_tag_name = 'select';
  public $element_attributes_default = [
    'name'     => 'select',
    'required' => 'required'
  ];
# ─────────────────────────────────────────────────────────────────────
  public $values = [];
  public $selected = [];
  public $disabled = [];

  function build() {
    parent::build();
    foreach ($this->values as $c_id => $c_data) {
      if (is_object($c_data) &&
             !empty($c_data->title) &&
             !empty($c_data->values)) {
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
    $option = new markup('option', $attr, ['content' => $title]);
    $option->attribute_insert('value', $value === 'not_selected' ? '' : $value);
    if (isset($this->selected[$value])) $option->attribute_insert('selected', 'selected');
    if (isset($this->disabled[$value])) $option->attribute_insert('disabled', 'disabled');
    $parent_el = $grp_id ? $this->child_select('element')->child_select($grp_id) :
                           $this->child_select('element');
    $parent_el->child_insert($option, $value);
  }

}}