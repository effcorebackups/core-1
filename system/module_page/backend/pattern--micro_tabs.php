<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class micro_tabs extends node {

  public $element_attributes = ['role' => 'micro_tabs-item'];
  public $values  = [];
  public $checked = [];

  function __construct($values = null, $checked = null) {
    if ($values ) $this->values   = $values;
    if ($checked) $this->checked  = $checked;
    parent::__construct();
  }

  function build() {
    if (!$this->is_builded) {
      foreach ($this->values as $c_value => $c_info) {
        if (!$this->child_select($c_value)) {
          if (is_string($c_info)) $c_info = (object)['title' => $c_info, 'element_attributes' => ['value' => $c_value], 'weight' => 0];
          $c_field                     = new field_radiobutton;
          $c_field->tag_name           = null;
          $c_field->template           = 'container_content';
          $c_field->title              = $c_info->title;
          $c_field->element_attributes = $c_info->element_attributes + $this->attributes_select('element_attributes') + $c_field->attributes_select('element_attributes');
          $c_field->weight             = $c_info->weight;
          $c_field->build();
                          $this->child_insert($c_field, $c_value);
        } else $c_field = $this->child_select($c_value);
        $c_field->checked_set(isset($this->checked[$c_value])); }
      $this->is_builded = true;
    }
  }

}}