<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_fields extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'fields'];
  public $unique_prefix = '';
  public $item_title = 'Item';

  function __construct($unique_prefix = null, $attributes = [], $weight = 0) {
    if ($unique_prefix) $this->unique_prefix = $unique_prefix;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $this->child_insert($this->widget_manage_group_get(), 'manage');
      $this->child_insert($this->widget_insert_get      (), 'insert');
      $this->widgets_group_manage_build();
      $this->is_builded = true;
    }
  }

  function widgets_group_manage_build() {
    $widgets_group_manage = $this->child_select('manage');
    $items = $this->items_get();
  # insert new and update existing widgets
    foreach ($this->items_get() as $c_row_id => $c_item) {
      if ($widgets_group_manage->child_select($c_row_id) != null) {$c_widget =                                                                     $widgets_group_manage->child_select(           $c_row_id);}
      if ($widgets_group_manage->child_select($c_row_id) == null) {$c_widget = $this->widget_manage_get($c_item, $c_row_id, $this->unique_prefix); $widgets_group_manage->child_insert($c_widget, $c_row_id);}
      $c_widget->weight = $c_widget->child_select('weight')->value_get();
    }
  # delete old widgets
    foreach ($widgets_group_manage->children_select() as $c_row_id => $c_widget) {
      if (!isset($items[$c_row_id])) {
        $widgets_group_manage->child_delete($c_row_id);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_group_get() {
    return new markup('x-widgets-group', [
      'data-type'              => 'manage',
      'data-has-rearrangeable' => 'true'
    ]);
  }

  function widget_manage_get($item, $c_row_id, $prefix) {
    $widget_manage = new markup('x-widget', [
      'data-rearrangeable'         => 'true',
      'data-fields-is-inline-full' => 'true'], [], $item->weight);
  # field for weight
    $field_weight = new field_weight;
    $field_weight->description_state = 'hidden';
    $field_weight->build();
    $field_weight->name_set($prefix.'weight'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
  # field for text
    $field_text = new field_text;
    $field_text->description_state = 'hidden';
    $field_text->build();
    $field_text->name_set($prefix.'text'.$c_row_id);
    $field_text->value_set($item->text);
  # button for delete item
    $button_delete = new button(null, ['data-style' => 'narrow-delete', 'title' => new text('delete')]);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($prefix.'button_delete'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
  # group the fields in widget 'manage'
    $widget_manage->child_insert($field_weight,  'weight'       );
    $widget_manage->child_insert($field_text,    'text'         );
    $widget_manage->child_insert($button_delete, 'button_delete');
    return $widget_manage;
  }

  function widget_insert_get() {
    $button_insert = new button('insert', ['title' => new text('insert')]);
    $button_insert->break_on_validate = true;
    $button_insert->build();
    $button_insert->value_set($this->unique_prefix.'button_insert');
    return $button_insert;
  }

  # ─────────────────────────────────────────────────────────────────────

  function items_get() {
    return $this->cform->validation_cache_get($this->unique_prefix.'items') ?: [];
  }

  function items_set($items) {
    $this->cform->validation_cache_is_persistent = true;
    $this->cform->validation_cache_set($this->unique_prefix.'items', $items);
    $this->widgets_group_manage_build();
  }

  function items_set_once($items) {
    if ($this->cform->validation_cache_get($this->unique_prefix.'items') === null) {
      $this->items_set($items ?: []);
    }
  }

  function items_reset() {
    $this->cform->validation_cache_is_persistent = false;
    $this->cform->validation_cache_set($this->unique_prefix.'items', null);
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_cache_update($form, $npath) {
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item) {
      $c_item->weight = (int)(field::request_value_get($this->unique_prefix.'weight'.$c_row_id));
      $c_item->text   =       field::request_value_get($this->unique_prefix.'text'.  $c_row_id);}
    $this->items_set($items);
  }

  function on_button_click_insert($form, $npath) {
    $min_weight = 0;
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item)
      $min_weight = min($min_weight, $c_item->weight);
    $new_item = new \stdClass;
    $new_item->id = 0;
    $new_item->weight = $min_weight - 5;
    $new_item->text = '';
    $items[] = $new_item;
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes with "%%_button" button!'], [
      'type'   => translation::get($this->item_title),
      'button' => translation::get('update')]));
    return true;
  }

  function on_button_click_delete($form, $npath, $button) {
    $items = $this->items_get();
    unset($items[$button->_id]);
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was deleted.',
      'Do not forget to save the changes with "%%_button" button!'], [
      'type'   => translation::get($this->item_title),
      'button' => translation::get('update')]));
    return true;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_request_value_set(&$group, $form, $npath) {
    $group->on_cache_update($form, $npath);
  }

  static function on_submit(&$group, $form, $npath) {
    $button_insert = $group->child_select('insert');
    if ($button_insert->is_clicked()) {
      return $group->on_button_click_insert($form, $npath);
    } else {
      foreach ($group->children_select_recursive() as $c_child) {
        if ($form->clicked_button === $c_child         &&
                               !empty($c_child->_type) &&
                                     ($c_child->_type) == 'delete') {
          return $group->on_button_click_delete(
            $form, $npath, $c_child
          );
        }
      }
    }
  }

}}