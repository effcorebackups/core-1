<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_fields extends widget_fields {

  public $attributes = ['data-type' => 'selection_fields'];
  public $item_title = 'Field';

  function widget_manage_get($item, $c_row_id, $prefix, $entity_name = 'demo_data', $entity_field_name = 'id') {
  # field for weight
    $field_weight = new field_weight;
    $field_weight->description_state = 'hidden';
    $field_weight->build();
    $field_weight->name_set($prefix.'weight'.$c_row_id);
    $field_weight->required_set(false);
    $field_weight->value_set($item->weight);
  # data markup
    $entity = entity::get($entity_name);
    $entity_field = $entity ? $entity->field_get($entity_field_name) : null;
    $data_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST PART'),
        'id'    => new markup('x-id',    [], [
                   new text_simple($entity_name      ), '.',
                   new text_simple($entity_field_name)]) ]);
  # button for delete item
    $button_delete = new button(null, ['data-style' => 'narrow-delete', 'title' => new text('delete')]);
    $button_delete->break_on_validate = true;
    $button_delete->build();
    $button_delete->value_set($prefix.'button_delete'.$c_row_id);
    $button_delete->_type = 'delete';
    $button_delete->_id = $c_row_id;
  # group the fields in widget 'manage'
    $widget_manage = new markup('x-widget', [
      'data-rearrangeable'         => 'true',
      'data-fields-is-inline-full' => 'true'], [], $item->weight);
    $widget_manage->child_insert($field_weight,  'weight'       );
    $widget_manage->child_insert($data_markup,   'data'         );
    $widget_manage->child_insert($button_delete, 'button_delete');
    return $widget_manage;
  }

}}