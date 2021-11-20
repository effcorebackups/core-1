<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_items extends control implements complex_control {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'items'];
  public $item_title = 'Item';
  public $title;
  public $title_tag_name = 'label';
  public $title_position = 'top'; # opener not working in 'bottom' mode
  public $title_attributes = ['data-widget-title' => true];
  public $content_tag_name = 'x-widget-content';
  public $content_attributes = ['data-widget-content' => true, 'data-nested-content' => true];
  public $state = 'opened'; # '' | opened | closed[checked]
  public $name_complex = 'widget_items';
  public $controls = [];
  public $number;

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $this->child_insert(static::widget_manage_group_get($this), 'manage');
      $this->child_insert(static::widget_insert_get      ($this), 'insert');
      $this->build_widget_manage_group();
      if ($this->number === null)
          $this->number = static::current_number_generate();
      $this->is_builded = true;
    }
  }

  function build_widget_manage_group() {
    $group = $this->child_select('manage');
    $items = $this->items_get();
  # insert new and update existing widgets
    foreach ($this->items_get() as $c_row_id => $c_item) {
      if ($group->child_select($c_row_id) === null) {$c_widget = static::widget_manage_get($this, $c_item, $c_row_id); $group->child_insert($c_widget, $c_row_id);}
      if ($group->child_select($c_row_id) !== null) {$c_widget =                                                       $group->child_select(           $c_row_id);}
      $c_widget->weight = $c_widget->child_select('weight')->value_get();
    }
  # delete old widgets
    foreach ($group->children_select() as $c_row_id => $c_widget) {
      if (!isset($items[$c_row_id]) || !empty($items[$c_row_id]->is_deleted)) {
        $group->child_delete($c_row_id);
      }
    }
  # message 'no items'
    if ($group->children_select_count() !== 0) $group->child_delete(                                          'no_items');
    if ($group->children_select_count() === 0) $group->child_insert(new markup('x-no-items', [], 'no items'), 'no_items');
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function name_get_complex() {
    return $this->name_complex;
  }

  function value_get_complex()                      {return $this->items_get();             }
  function value_set_complex($value, $once = false) {       $this->items_set($value, $once);}

  function disabled_get() {
    return false;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function items_get() {
    return $this->cform->validation_cache_get($this->name_get_complex().'__items') ?: [];
  }

  function items_set($items, $once = false) {
    if ($once && $this->cform->validation_cache_get($this->name_get_complex().'__items') !== null) return;
    $this->cform->validation_cache_is_persistent = true;
    $this->cform->validation_cache_set($this->name_get_complex().'__items', $items ?: []);
    $this->build_widget_manage_group();
  }

  function items_reset() {
    $this->cform->validation_cache_is_persistent = false;
    $this->cform->validation_cache_set($this->name_get_complex().'__items', null);
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function render_self() {
    if ($this->title) {
      $html_name = 'f_widget_opener_'.$this->number;
      $opener = $this->render_opener();
      if ((bool)$this->title_is_visible === true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name                         ], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener !== '') return $opener.(new markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name, 'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible !== true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                     'aria-hidden' => 'true'], $this->title))->render();
      if ((bool)$this->title_is_visible === true && $opener === '') return         (new markup($this->title_tag_name, $this->title_attributes + [                                            ], $this->title))->render();
    }
  }

  function render_opener() {
    if ($this->state === 'opened' ||
        $this->state === 'closed') {
      $html_name    = 'f_widget_opener_'.$this->number;
      $form_id      = request::value_get('form_id');
      $submit_value = request::value_get($html_name);
      $has_error    = $this->has_error_in();
      if ($form_id === '' && $this->state === 'opened'                    ) /*               default = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
      if ($form_id === '' && $this->state === 'closed'                    ) /*               default = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
      if ($form_id !== '' && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
      if ($form_id !== '' && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
      if ($form_id !== '' && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
      if ($form_id !== '' && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'title', 'title' => new text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
    }
    return '';
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $c_number = 0;

  static function current_number_generate() {
    return static::$c_number++;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function widget_manage_group_get($widget) {
    return new markup('x-widgets-group', [
      'data-type' => 'manage',
      'data-has-rearrangeable' => true
    ]);
  }

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = new markup('x-widget', [
      'data-rowid'         => $c_row_id,
      'data-rearrangeable' => true], [], $item->weight);
  # control for weight
    $field_weight = new field_weight(null, null, [], +400);
    $field_weight->attributes['data-style'] = 'inline';
    $field_weight->description_state = 'hidden';
    $field_weight->cform = $widget->cform;
    $field_weight->build();
    $field_weight->name_set($widget->name_get_complex().'__weight__'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
  # button for deletion of the old item
    $button_delete = new button(null, ['data-style' => 'delete little', 'title' => new text('delete')], +500);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($widget->name_get_complex().'__delete__'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
  # relate new controls with the widget
    $widget->controls['#weight__'.$c_row_id] = $field_weight;
    $widget->controls['~delete__'.$c_row_id] = $button_delete;
    $result->child_insert($field_weight, 'weight');
    $result->child_insert($button_delete, 'button_delete');
    return $result;
  }

  static function widget_insert_get($widget) {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # button for insertion of the new item
    $button = new button('insert', ['title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($widget->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $widget->controls['~insert'] = $button;
    $result->child_insert($button, 'button');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_button_click_insert($widget, $form, $npath, $button) {
    $min_weight = 0;
    $items = $widget->items_get();
    foreach ($items as $c_row_id => $c_item)
      $min_weight = min($min_weight, $c_item->weight);
    $new_item = new \stdClass;
    $new_item->weight = count($items) ? $min_weight - 5 : 0;
    $items[] = $new_item;
    $new_item->id = 0;
    $widget->items_set($items);
    message::insert(new text_multiline([
      'Item of type "%%_type" was inserted.',
      'Do not forget to save the changes!'], [
      'type' => (new text($widget->item_title))->render() ]));
    return true;
  }

  static function on_button_click_delete($widget, $form, $npath, $button) {
    $items = $widget->items_get();
    $item_id = $items[$button->_id]->id ?? null;
    unset($items[$button->_id]);
    $widget->items_set($items);
    if ($item_id) message::insert(new text_multiline(['Item of type "%%_type" with ID = "%%_id" was deleted.', 'Do not forget to save the changes!'], ['type' => (new text($widget->item_title))->render(), 'id' => $item_id ]));
    else          message::insert(new text_multiline(['Item of type "%%_type" was deleted.',                   'Do not forget to save the changes!'], ['type' => (new text($widget->item_title))->render()                   ]));
    return true;
  }

  static function on_request_value_set($widget, $form, $npath) {
    $items = $widget->items_get();
    foreach ($items as $c_row_id => $c_item)
      if (isset($widget->controls['#weight__'.$c_row_id]))
        $c_item->weight = (int)$widget->controls['#weight__'.$c_row_id]->value_get();
    $widget->items_set($items);
  }

  static function on_submit($widget, $form, $npath) {
    foreach ($widget->controls as $c_button) {
      if ($c_button instanceof button && $c_button->is_clicked()) {
        if (isset($c_button->_type) && $c_button->_type === 'insert') return event::start_local('on_button_click_insert', $widget, ['form' => $form, 'npath' => $npath, 'button' => $c_button]);
        if (isset($c_button->_type) && $c_button->_type === 'delete') return event::start_local('on_button_click_delete', $widget, ['form' => $form, 'npath' => $npath, 'button' => $c_button]);
      }
    }
  }

}}