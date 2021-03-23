<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_entity_fields extends widget_items {

  public $title = 'Fields';
  public $title__not_selected__widget_insert = '- select -';
  public $item_title = 'Field';
  public $attributes = ['data-type' => 'items-entity_fields'];
  public $name_complex = 'widget_entity_fields';

  function widget_manage_get($item, $c_row_id) {
    $widget = parent::widget_manage_get($item, $c_row_id);
  # info markup
    $entity = entity::get($item->entity_name);
    $entity_field = $entity ? $entity->field_get($item->entity_field_name) : null;
    $title_markup = isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST FIELD';
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $title_markup),
        'id'    => new markup('x-id',    [], [
                   new text_simple($item->entity_name      ), '.',
                   new text_simple($item->entity_field_name)]) ]);
  # grouping of previous elements in widget 'manage'
    $widget->child_insert($info_markup, 'info');
    return $widget;
  }

  function widget_insert_get() {
    $widget = new markup('x-widget', [
      'data-type' => 'insert']);
  # control with type of new item
    $entities = entity::get_all();
    core::array_sort_by_text_property($entities);
    $options = ['not_selected' => $this->title__not_selected__widget_insert];
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
    $select = new field_select('New field');
    $select->values = $options;
    $select->build();
    $select->name_set($this->name_get_complex().'__insert');
    $select->required_set(false);
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'narrow-insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($this->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $this->controls['#insert'] = $select;
    $this->controls['~insert'] = $button;
    $widget->child_insert($select, 'select');
    $widget->child_insert($button, 'button');
    return $widget;
  }

  # ─────────────────────────────────────────────────────────────────────

  function on_button_click_insert($form, $npath, $button) {
    $this->controls['#insert']->required_set(true);
    if (field_select::on_validate($this->controls['#insert'], $form, $npath)) {
      $this->controls['#insert']->required_set(false);
      $params = explode('|', $this->controls['#insert']->value_get());
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
      $this->controls['#insert']->value_set('');
      message::insert(new text_multiline([
        'Item of type "%%_type" was inserted.',
        'Do not forget to save the changes!'], [
        'type' => (new text($this->item_title))->render() ]));
      return true;
    }
  }

}}