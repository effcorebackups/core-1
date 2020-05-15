<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_attributes extends widget_items {

  public $title = 'Attributes';
  public $item_title = 'Attribute';
  public $attributes = ['data-type' => 'items-attributes'];
  public $name_complex = 'widget_attributes';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # control for attribute name
    $field_name = new field_text;
    $field_name->title = 'Name';
    $field_name->description_state = 'hidden';
    $field_name->build();
    $field_name->name_set($this->name_complex.'__name__'.$c_row_id);
    $field_name->value_set($item->name);
    $this->controls['#name__'.$c_row_id] = $field_name;
  # control for attribute value
    $field_value = new field_text;
    $field_value->title = 'Val.';
    $field_value->description_state = 'hidden';
    $field_value->build();
    $field_value->name_set($this->name_complex.'__value__'.$c_row_id);
    $field_value->value_set($item->value);
    $field_value->required_set(false);
    $field_value->maxlength_set(2048);
    $this->controls['#value__'.$c_row_id] = $field_value;
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($field_name,  'name');
    $widget->child_insert($field_value, 'value');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_cache_update($form, $npath) {
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item) {
      if (isset($this->controls['#weight__'.$c_row_id])) $c_item->weight = (int)$this->controls['#weight__'.$c_row_id]->value_get();
      if (isset($this->controls['#name__'.  $c_row_id])) $c_item->name   =      $this->controls['#name__'.  $c_row_id]->value_get();
      if (isset($this->controls['#value__'. $c_row_id])) $c_item->value  =      $this->controls['#value__'. $c_row_id]->value_get();}
    $this->items_set($items);
  }

  function on_button_click_insert($form, $npath, $button) {
    $min_weight = 0;
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item)
      $min_weight = min($min_weight, $c_item->weight);
    $new_item = new \stdClass;
    $new_item->weight = count($items) ? $min_weight - 5 : 0;
    $new_item->name   = '';
    $new_item->value  = '';
    $items[] = $new_item;
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes!'], [
      'type' => translation::apply($this->item_title)]));
    return true;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function complex_value_to_value($complex) {
    core::array_sort_by_weight($complex);
    $attributes = [];
    foreach ($complex as $c_complex)
      $attributes[$c_complex->name] = $c_complex->name;
    return core::data_to_attr($attributes);
  }

}}