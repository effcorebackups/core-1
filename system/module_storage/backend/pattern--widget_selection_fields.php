<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_fields extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'selection_fields'];

  function __construct($attributes = [], $weight = 0) {
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $widgets_group_manage = new markup('x-widgets-group', [
        'data-type'              => 'manage',
        'data-has-rearrangeable' => 'true']);
    # widgets for manage each item
      $c_widget_manage_weight = 0;
      foreach ($this->items_get() as $c_row_id => $c_info) {
        $c_widget_manage = new widget_selection_field_manage($c_info->entity_name, $c_info->entity_field_name, [], $c_widget_manage_weight);
        $c_widget_manage->build();
        $c_widget_manage_weight -= 5;
        $widgets_group_manage->child_insert($c_widget_manage, $c_row_id);
        $c_widget_manage->on_click_delete_handler = function ($group, $form, $npath) {
          $this->on_click_delete($group, $form, $npath);
        };
      }
    # widget for insert new item
      $widget_insert = new widget_selection_field_insert;
      $widget_insert->build();
      $widget_insert->on_click_insert_handler = function ($group, $form, $npath, $value) {
        $this->on_click_insert($group, $form, $npath, $value);
      };
    # insert all widgets
      $this->child_insert($widgets_group_manage, 'manage');
      $this->child_insert($widget_insert, 'insert');
      $this->is_builded = true;
    }
  }

  function items_get_sorted() {
    $buffer = [];
    $result = [];
    foreach ($this->items_get() as $c_row_id => $c_object) {
      $c_field_name_suffix = $c_object->entity_name.'_'.$c_object->entity_field_name;
      $c_weight = (int)(field::request_value_get('weight_'.$c_field_name_suffix));
      $c_buffer_new_item = new \stdClass;
      $c_buffer_new_item->row_id = $c_row_id;
      $c_buffer_new_item->weight = $c_weight;
      $c_buffer_new_item->object = $c_object;
      $buffer[] = $c_buffer_new_item;}
    core::array_sort_by_weight($buffer);
    foreach ($buffer as $c_sorted)
      $result[$c_sorted->row_id] =
              $c_sorted->object;
    return $result;
  }

  function items_get() {
    return $this->cform->validation_cache_get('fields') ?: [];
  }

  function items_set($items) {
    $this->cform->validation_cache_is_persistent = true;
    $this->cform->validation_cache_set('fields', $items);
    if ($this->is_builded) {
        $this->is_builded = false;
        $this->build();
    }
  }

  function items_set_once($items) {
    if ($this->cform->validation_cache_get('fields') === null) {
      $this->items_set($items);
    }
  }

  function on_click_insert($group, $form, $npath, $value) {
    $fields = $this->items_get();
    $entity_info = explode('.', $value);
    $fields[$value] = new \stdClass;
    $fields[$value]->type = 'field';
    $fields[$value]->entity_name       = $entity_info[0];
    $fields[$value]->entity_field_name = $entity_info[1];
    $this->items_set($fields);
  # report
    $entity = entity::get(             $entity_info[0]);
    $entity_field = $entity->field_get($entity_info[1]);
    message::insert(new text_multiline([
      'Field "%%_title" (%%_id) was inserted.',
      'Click the button "%%_name" to save your changes!'], [
      'title' => translation::get($entity->title).': '.translation::get($entity_field->title),
      'id'    => $entity_info[0].'.'.$entity_info[1],
      'name'  => translation::get('update')]));
    return true;
  }

  function on_click_delete($group, $form, $npath) {
    $fields = $this->items_get();
    foreach ($fields as $c_row_id => $c_field) {
      if ($c_field->type              == 'field'             &&
          $c_field->entity_name       == $group->entity_name &&
          $c_field->entity_field_name == $group->entity_field_name) {
        unset($fields[$c_row_id]);
        $this->items_set($fields);
      # report
        $entity = entity::get($group->entity_name);
        $entity_field = $entity ? $entity->field_get($group->entity_field_name) : null;
        message::insert(new text_multiline([
          'Field "%%_title" (%%_id) was deleted.',
          'Click the button "%%_name" to save your changes!'], [
          'title' => isset($entity_field->title) ? translation::get($entity->title).': '.translation::get($entity_field->title) : 'LOST PART',
          'id'    => $group->entity_name.'.'.$group->entity_field_name,
          'name'  => translation::get('update')]));
        return true;
      }
    }
  }

}}