<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select_entity_field_name extends field_select {

  public $title = 'Field name';
  public $title__not_selected = '- select -';
  public $attributes = ['data-type' => 'entity_field_name'];
  public $element_attributes = [
    'name'     => 'entity_field_name',
    'required' => true
  ];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $items = [];
      $entities = entity::get_all();
      core::array_sort_by_string($entities);
      foreach ($entities as $c_entity) {
        if (!empty($c_entity->managing_is_enabled)) {
          foreach ($c_entity->fields as $c_field_name => $c_field_info) {
            if (!isset($items[$c_entity->name])) {
                       $items[$c_entity->name] = new \stdClass;
                       $items[$c_entity->name]->title = $c_entity->title; }
            $c_text_object = new text_multiline(['title' => $c_field_info->title, 'id' => '('.$c_entity->name.'.'.$c_field_name.')'], [], ' ');
            $c_text_object->_text_translated = $c_text_object->render();
            $items[$c_entity->name]->items[$c_entity->name.'|'.$c_field_name] = $c_text_object;
          }
          core::array_sort_by_string(
            $items[$c_entity->name]->items, '_text_translated', 'd', false
          );
        }
      }
      $this->items = ['not_selected' => $this->title__not_selected] + $items;
      $this->is_builded = false;
      parent::build();
    }
  }

}}