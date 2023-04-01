<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select_entity_name extends field_select {

  public $title = 'Entity name';
  public $title__not_selected = '- select -';
  public $attributes = ['data-type' => 'entity_name'];
  public $element_attributes = [
    'name'     => 'entity_name',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $items = [];
      foreach (entity::get_all() as $c_entity) {
        if (!empty($c_entity->managing_is_enabled)) {
          $c_text_object = new text_multiline(['title' => $c_entity->title, 'id' => '('.$c_entity->name.')'], [], ' ');
          $c_text_object->_text_translated = $c_text_object->render();
          $items[$c_entity->name] = $c_text_object; }}
      core::array_sort_by_string($items, '_text_translated', 'd', false);
      $this->items = ['not_selected' => $this->title__not_selected] + $items;
      $this->is_builded = false;
      parent::build();
    }
  }

}}