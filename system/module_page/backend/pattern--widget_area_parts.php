<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_parts extends widget_fields {

  public $attributes = ['data-type' => 'area_parts'];
  public $item_title = 'Part';
  public $id_area;

  function __construct($unique_prefix, $id_area, $attributes = [], $weight = 0) {
    $this->unique_prefix = $unique_prefix;
    $this->id_area       = $id_area;
    parent::__construct($unique_prefix, $attributes, $weight);
  }

  function widget_manage_get($item, $c_row_id, $prefix) {
    $widget = new markup('x-widget', [
      'data-rearrangeable'         => 'true',
      'data-fields-is-inline-full' => 'true'], [], $item->weight);
  # field for weight
    $field_weight = new field_weight;
    $field_weight->description_state = 'hidden';
    $field_weight->build();
    $field_weight->name_set($prefix.'weight'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
    $this->_fields['weight'.$c_row_id] = $field_weight;
  # data markup
    $preset = page_part_preset::select($item->id);
    $data_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $preset ? [$preset->managing_group, ': ', $preset->managing_title] : 'LOST PART'),
        'id'    => new markup('x-id',    [], new text_simple($preset->id)) ]);
  # button for deletion of the old item
    $button_delete = new button(null, ['data-style' => 'narrow-delete', 'title' => new text('delete')]);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($prefix.'delete'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
    $this->_buttons['delete'.$c_row_id] = $button_delete;
  # group the previous elements in widget 'manage'
    $widget->child_insert($field_weight,  'weight'       );
    $widget->child_insert($data_markup,   'data'         );
    $widget->child_insert($button_delete, 'button_delete');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # field for selection of the type of new item
    $presets = page_part_preset::select_all($this->id_area);
    core::array_sort_by_text_property($presets, 'managing_group');
    $options = ['not_selected' => '- no -'];
    foreach ($presets as $c_preset) {
      $c_group_id = core::sanitize_id($c_preset->managing_group);
      if (!isset($options[$c_group_id])) {
                 $options[$c_group_id] = new \stdClass;
                 $options[$c_group_id]->title = $c_preset->managing_group;}
      $options[$c_group_id]->values[$c_preset->id] = translation::get($c_preset->managing_title).' ('.$c_preset->id.')';
    }
    foreach ($options as $c_group) {
      if ($c_group instanceof \stdClass) {
        core::array_sort_text($c_group->values);
      }
    }
    $select = new field_select('Insert field');
    $select->values = $options;
    $select->build();
    $select->name_set($this->unique_prefix.'insert');
    $select->required_set(false);
    $this->_fields['insert'] = $select;
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->unique_prefix.'insert');
    $button->_type = 'insert';
    $this->_buttons['insert'] = $button;
  # group the previous elements in widget 'insert'
    $widget->child_insert($select, 'select');
    $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_button_click_insert($form, $npath, $button) {
    $new_value = $this->_fields['insert']->value_get();
    if ($new_value) {
      $min_weight = 0;
      $items = $this->items_get();
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new page_part_preset_link($new_value);
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $items[] = $new_item;
      $this->items_set($items);
      $this->_fields['insert']->value_set('');
      message::insert(new text_multiline([
        'Item of type "%%_type" was inserted.',
        'Do not forget to save the changes with "%%_button" button!'], [
        'type'   => translation::get($this->item_title),
        'button' => translation::get('update')]));
      return true;
    }
  }

}}