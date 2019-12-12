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
      $c_weight = 0;
      $widgets_manage_group = new markup('x-widgets-group', ['data-has-rearrangeable' => 'true']);
      foreach ($this->cform->validation_cache_get('fields') as $c_id => $c_info) {
        $c_widget_manage = new widget_selection_field_manage($c_info->entity_name, $c_info->entity_field_name, [], $c_weight);
        $c_widget_manage->build();
        $c_widget_manage->on_click_delete_handler = function ($group, $form, $npath) {$this->on_click_delete($group, $form, $npath);};
        $widgets_manage_group->child_insert($c_widget_manage, $c_id);
        $c_weight -= 5;}
      $widget_insert = new widget_selection_field_insert;
      $widget_insert->on_click_insert_handler = function ($group, $form, $npath, $value) {$this->on_click_insert($group, $form, $npath, $value);};
      $widget_insert->build();
      $this->child_insert($widgets_manage_group, 'widgets_manage_group');
      $this->child_insert($widget_insert, 'widget_insert');
      $this->is_builded = true;
    }
  }

  function on_click_insert($group, $form, $npath, $value) {
    $fields = $form->validation_cache_get('fields');
    $entity_info = explode('.', $value);
    $fields[$value] = (object)[
      'type'              => 'field',
      'entity_name'       => $entity_info[0],
      'entity_field_name' => $entity_info[1]];
    $form->validation_cache_is_persistent = true;
    $form->validation_cache_set('fields', $fields);
    $this->is_builded = false;
    $this->build();
  # report
    $entity = entity::get(             $entity_info[0]);
    $entity_field = $entity->field_get($entity_info[1]);
    message::insert(new text('Field "%%_title" (%%_id) was inserted.', ['title' => translation::get($entity->title).': '.translation::get($entity_field->title), 'id' => $entity_info[0].'.'.$entity_info[1]]));
    message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
    return true;
  }

  function on_click_delete($group, $form, $npath) {
    $fields = $form->validation_cache_get('fields');
    foreach ($fields as $c_row_id => $c_field) {
      if ($c_field->type              == 'field'             &&
          $c_field->entity_name       == $group->entity_name &&
          $c_field->entity_field_name == $group->entity_field_name) {
        unset($fields[$c_row_id]);
        $form->validation_cache_is_persistent = true;
        $form->validation_cache_set('fields', $fields);
        $this->is_builded = false;
        $this->build();
      # report
        $entity = entity::get($group->entity_name);
        $entity_field = $entity ? $entity->field_get($group->entity_field_name) : null;
        message::insert(new text('Field "%%_title" (%%_id) was deleted.', ['title' => isset($entity_field->title) ? translation::get($entity->title).': '.translation::get($entity_field->title) : 'LOST PART', 'id' => $group->entity_name.'.'.$group->entity_field_name]));
        message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
        return true;
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_submit(&$group, $form, $npath) {
  }

}}