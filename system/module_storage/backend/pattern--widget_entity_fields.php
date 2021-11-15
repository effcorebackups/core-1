<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_entity_fields extends widget_items {

  public $title = 'Fields';
  public $title__not_selected__widget_insert = '- select -';
  public $item_title = 'Field';
  public $attributes = ['data-type' => 'items-entity_fields'];
  public $name_complex = 'widget_entity_fields';

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
  # info markup
    $entity = entity::get($item->entity_name);
    $entity_field = $entity ? $entity->field_get($item->entity_field_name) : null;
    $title_markup = isset($entity_field->title) ?
                         [$entity      ->title, ': ',
                          $entity_field->title] : 'LOST FIELD';
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $title_markup),
        'id'    => new markup('x-id',    [], [
                   new text_simple($item->entity_name      ), '.',
                   new text_simple($item->entity_field_name)]) ]);
  # grouping of previous elements in widget 'manage'
    $result->child_insert($info_markup, 'info');
    return $result;
  }

  static function widget_insert_get($widget) {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # control with type of new item
    $entities = entity::get_all();
    core::array_sort_by_text_property($entities);
    $options = ['not_selected' => $widget->title__not_selected__widget_insert];
    foreach ($entities as $c_entity) {
      if (!empty($c_entity->managing_is_enabled)) {
        foreach ($c_entity->fields as $c_field_name => $c_field_info) {
          if (!isset($options[$c_entity->name])) {
                     $options[$c_entity->name] = new \stdClass;
                     $options[$c_entity->name]->title = $c_entity->title; }
          $options[$c_entity->name]->values['field|'.$c_entity->name.'|'.$c_field_name] = new text_multiline([
            'title' => $c_field_info->title, 'id' => '('.$c_entity->name.'.'.$c_field_name.')'], [], ' '
          );
        }
      }
    }
    $select = new field_select('New field');
    $select->values = $options;
    $select->cform = $widget->cform;
    $select->build();
    $select->name_set($widget->name_get_complex().'__insert');
    $select->required_set(false);
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($widget->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $widget->controls['#insert'] = $select;
    $widget->controls['~insert'] = $button;
    $result->child_insert($select, 'select');
    $result->child_insert($button, 'button');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_button_click_insert($widget, $form, $npath, $button) {
    $widget->controls['#insert']->required_set(true);
    $result_validation = field_select::on_validate($widget->controls['#insert'], $form, $npath);
    $widget->controls['#insert']->required_set(false);
    if ($result_validation) {
      $params = explode('|', $widget->controls['#insert']->value_get());
      $min_weight = 0;
      $items = $widget->items_get();
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new \stdClass;
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $new_item->type              = $params[0];
      $new_item->entity_name       = $params[1];
      $new_item->entity_field_name = $params[2];
      $items[] = $new_item;
      $widget->items_set($items);
      $widget->controls['#insert']->value_set('');
      message::insert(new text_multiline([
        'Item of type "%%_type" was inserted.',
        'Do not forget to save the changes!'], [
        'type' => (new text($widget->item_title))->render() ]));
      return true;
    }
  }

}}