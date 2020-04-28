<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_texts extends widget_items {

  public $attributes = ['data-type' => 'items'];
  public $name_complex = 'widget_texts';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # control for text
    $field_text = new field_text;
    $field_text->description_state = 'hidden';
    $field_text->build();
    $field_text->name_set($this->name_complex.'__text__'.$c_row_id);
    $field_text->value_set($item->text);
    $this->controls['#text__'.$c_row_id] = $field_text;
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($field_text, 'text');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_cache_update($form, $npath) {
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item) {
      if (isset($this->controls['#weight__'.$c_row_id])) $c_item->weight = (int)$this->controls['#weight__'.$c_row_id]->value_get();
      if (isset($this->controls['#text__'.  $c_row_id])) $c_item->text   =      $this->controls['#text__'.  $c_row_id]->value_get();}
    $this->items_set($items);
  }

  function on_button_click_insert($form, $npath, $button) {
    $min_weight = 0;
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item)
      $min_weight = min($min_weight, $c_item->weight);
    $new_item = new \stdClass;
    $new_item->weight = count($items) ? $min_weight - 5 : 0;
    $new_item->id     = 0;
    $new_item->text   = '';
    $items[] = $new_item;
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes!'], [
      'type' => translation::apply($this->item_title)]));
    return true;
  }

}}