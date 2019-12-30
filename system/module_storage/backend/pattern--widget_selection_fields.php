<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_fields extends widget_fields {

  public $attributes = ['data-type' => 'selection_fields'];
  public $item_title = 'Field';

  function widget_manage_get($item, $c_row_id, $prefix, $entity_name = 'demo_data', $entity_field_name = 'id') {
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
    $entity = entity::get($entity_name);
    $entity_field = $entity ? $entity->field_get($entity_field_name) : null;
    $data_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST PART'),
        'id'    => new markup('x-id',    [], [
                   new text_simple($entity_name      ), '.',
                   new text_simple($entity_field_name)]) ]);
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
    $entities = entity::get_all();
    core::array_sort_by_text_property($entities);
    $options = ['not_selected' => '- no -'];
    foreach ($entities as $c_entity) {
      if (!empty($c_entity->managing_is_enabled)) {
        foreach ($c_entity->fields as $c_field_name => $c_field_info) {
          if (!empty($c_field_info->managing_on_select_is_enabled)) {
            if (!isset($options[$c_entity->name])) {
                       $options[$c_entity->name] = new \stdClass;
                       $options[$c_entity->name]->title = $c_entity->title;}
            $options[$c_entity->name]->values['field|'.$c_entity->name.'|'.$c_field_name] = new text_multiline([
              'title' => $c_field_info->title, 'id' => '('.$c_entity->name.'.'.$c_field_name.')'], [], ' '
            );
          }
        }
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

  function on_button_click_insert($form, $npath, $button) {
    $new_value = $this->_fields['insert']->value_get();
    if ($new_value) {
      $params = explode('|', $new_value);
      $min_weight = 0;
      $items = $this->items_get();
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new \stdClass;
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $new_item->type              = $params[0];
      $new_item->entity_name       = $params[1];
      $new_item->entity_field_name = $params[2];
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