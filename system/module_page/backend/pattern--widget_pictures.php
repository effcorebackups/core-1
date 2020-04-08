<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_pictures extends widget_fields {

  public $title = 'Pictures';
  public $item_title = 'Picture';
  public $attributes = ['data-type' => 'fields-pictures'];
  public $name_complex = 'widget_pictures';
  public $name_prefix = 'picture';

  # ─────────────────────────────────────────────────────────────────────

  function widget_manage_get($item, $c_row_id) {
    $widget = new markup('x-widget', [
      'data-rearrangeable'         => 'true',
      'data-fields-is-inline-full' => 'true'], [], $item->weight);
  # field for weight
    $field_weight = new field_weight;
    $field_weight->description_state = 'hidden';
    $field_weight->build();
    $field_weight->name_set($this->name_prefix.'__weight__'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
    $this->_fields['weight__'.$c_row_id] = $field_weight;
  # button for deletion of the old item
    $button_delete = new button(null, ['data-style' => 'narrow-delete', 'title' => new text('delete')]);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($this->name_prefix.'__delete__'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
    $this->_buttons['delete__'.$c_row_id] = $button_delete;
  # group the previous elements in widget 'manage'
    $widget->child_insert($field_weight,  'weight'       );
    $widget->child_insert($button_delete, 'button_delete');
    return $widget;
  }

}}