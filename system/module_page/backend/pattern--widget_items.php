<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_items extends control implements complex_control {

  public $title = null;
  public $title_tag_name = 'label';
  public $title_position = 'top'; # opener not working in 'bottom' mode
  public $title_attributes = ['data-widget-title' => true];
  public $state = 'opened'; # '' | opened | closed[checked]
  public $number = 0;

  public $item_title = 'Item';
  public $tag_name         = 'x-widget';
  public $content_tag_name = 'x-widget-content';
  public $attributes = ['data-type' => 'items'];
  public $name_complex = 'widget_items';
  public $controls = [];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $this->child_insert($this->widget_manage_group_get(), 'manage');
      $this->child_insert($this->widget_insert_get      (), 'insert');
      $this->widgets_manage_group_build();
      $this->number = static::current_number_get();
      $this->is_builded = true;
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function name_get_complex() {
    return $this->name_complex;
  }

  function value_get_complex()                      {return $this->items_get();             }
  function value_set_complex($value, $once = false) {       $this->items_set($value, $once);}

  function disabled_get() {
    return false;
  }

  # ─────────────────────────────────────────────────────────────────────

  function items_get() {
    return $this->cform->validation_cache_get($this->name_complex.'__items') ?: [];
  }

  function items_set($items, $once = false) {
    if ($once && $this->cform->validation_cache_get($this->name_complex.'__items') !== null) return;
    $this->cform->validation_cache_is_persistent = true;
    $this->cform->validation_cache_set($this->name_complex.'__items', $items ?: []);
    $this->widgets_manage_group_build();
  }

  function items_reset() {
    $this->cform->validation_cache_is_persistent = false;
    $this->cform->validation_cache_set($this->name_complex.'__items', null);
  }

  # ─────────────────────────────────────────────────────────────────────

  function widgets_manage_group_build() {
    $group = $this->child_select('manage');
    $items = $this->items_get();
  # insert new and update existing widgets
    foreach ($this->items_get() as $c_row_id => $c_item) {
      if ($group->child_select($c_row_id) == null) {$c_widget = $this->widget_manage_get($c_item, $c_row_id); $group->child_insert($c_widget, $c_row_id);}
      if ($group->child_select($c_row_id) != null) {$c_widget =                                               $group->child_select(           $c_row_id);}
      $c_widget->weight = $c_widget->child_select('weight')->value_get();
    }
  # delete old widgets
    foreach ($group->children_select() as $c_row_id => $c_widget) {
      if (!isset($items[$c_row_id]) || !empty($items[$c_row_id]->is_deleted)) {
        $group->child_delete($c_row_id);
      }
    }
  # message 'no items'
    if (count($group->children_select()) != 0) $group->child_delete(                                          'no_items');
    if (count($group->children_select()) == 0) $group->child_insert(new markup('x-no-items', [], 'no items'), 'no_items');
  }

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_group_get() {
    return new markup('x-widgets-group', [
      'data-type' => 'manage',
      'data-has-rearrangeable' => true
    ]);
  }

  function widget_manage_get($item, $c_row_id) {
    $widget = new markup('x-widget', [
      'data-rearrangeable'           => true,
      'data-controls-is-inline-full' => true], [], $item->weight);
  # control for weight
    $field_weight = new field_weight;
    $field_weight->description_state = 'hidden';
    $field_weight->build();
    $field_weight->name_set($this->name_complex.'__weight__'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
    $this->controls['#weight__'.$c_row_id] = $field_weight;
  # button for deletion of the old item
    $button_delete = new button(null, ['data-style' => 'narrow-delete-zoomed', 'title' => new text('delete')]);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($this->name_complex.'__delete__'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
    $this->controls['~delete__'.$c_row_id] = $button_delete;
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($field_weight,  'weight'       );
    $widget->child_insert($button_delete, 'button_delete');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # button for insertion of the new item
    $button = new button('insert', ['title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_complex.'__insert');
    $button->_type = 'insert';
    $this->controls['~insert'] = $button;
  # grouping of previous elements in widget 'insert'
    $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_cache_update($form, $npath) {
    $items = $this->items_get();
    foreach ($items as $c_row_id => $c_item)
      if (isset($this->controls['#weight__'.$c_row_id])) $c_item->weight = (int)$this->controls['#weight__'.$c_row_id]->value_get();
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
    $items[] = $new_item;
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes!'], [
      'type' => (new text($this->item_title))->render() ]));
    return true;
  }

  function on_button_click_delete($form, $npath, $button) {
    $items = $this->items_get();
    unset($items[$button->_id]);
    $this->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was deleted.',
      'Do not forget to save the changes!'], [
      'type' => (new text($this->item_title))->render() ]));
    return true;
  }

  # ─────────────────────────────────────────────────────────────────────

  function render_self() {
    if ($this->title) {
      $opener = $this->render_opener();
      if ($this->title_is_visible != 0 && $opener != '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_widget_opener_'.$this->number                         ], $this->title))->render();
      if ($this->title_is_visible == 0 && $opener != '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => 'f_widget_opener_'.$this->number, 'aria-hidden' => 'true'], $this->title))->render();
      if ($this->title_is_visible == 0 && $opener == '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                           'aria-hidden' => 'true'], $this->title))->render();
      if ($this->title_is_visible != 0 && $opener == '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                                                  ], $this->title))->render();
    }
  }

  function render_opener() {
    switch ($this->state) {
      case 'opened': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => 'f_widget_opener_'.$this->number, 'id' => 'f_widget_opener_'.$this->number                   ]); break;
      case 'closed': $opener = new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => 'f_widget_opener_'.$this->number, 'id' => 'f_widget_opener_'.$this->number, 'checked' => true]); break;
      default      : $opener = null;
    }
    if ($opener && field::request_value_get('form_id') && field::request_value_get('f_widget_opener_'.$this->number) == 'on') $opener->attribute_insert('checked', true);
    if ($opener && field::request_value_get('form_id') && field::request_value_get('f_widget_opener_'.$this->number) != 'on') $opener->attribute_delete('checked'      );
    if ($opener && $this->has_error_in_container()                                                                          ) $opener->attribute_delete('checked'      );
    return $opener ?
           $opener->render() : '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function current_number_get() {
    return static::$c_number++;
  }

  static function on_request_value_set(&$widget, $form, $npath) {
    $widget->on_cache_update($form, $npath);
  }

  static function on_submit(&$widget, $form, $npath) {
    foreach ($widget->controls as $c_button) {
      if ($c_button instanceof button && $c_button->is_clicked()) {
        if (isset($c_button->_type) && $c_button->_type == 'insert') return $widget->on_button_click_insert($form, $npath, $c_button);
        if (isset($c_button->_type) && $c_button->_type == 'delete') return $widget->on_button_click_delete($form, $npath, $c_button);
        return;
      }
    }
  }

}}