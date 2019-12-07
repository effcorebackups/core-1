<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_field_manage extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'selection_field-manage'];
  public $entity_name;
  public $entity_field_name;

  function __construct($entity_name, $entity_field_name, $attributes = [], $weight = 0) {
    $this->entity_name       = $entity_name;
    $this->entity_field_name = $entity_field_name;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

 function build() {
    if (!$this->is_builded) {
      $entity = entity::get($this->entity_name);
      $entity_field = $entity ? $entity->field_get($this->entity_field_name) : null;
      $button_delete = new button('', ['data-style' => 'narrow-delete', 'title' => new text('Delete')]);
      $button_delete->build();
      $button_delete->value_set('button_field_delete_'.$this->entity_name.'_'.$this->entity_field_name);
      $this->child_insert($button_delete, 'button_delete');
      $this->child_insert(new markup('x-title', [], isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST PART'), 'title');
      $this->child_insert(new markup('x-id',    [], [
        new text_simple($this->entity_name), '.',
        new text_simple($this->entity_field_name)]), 'id');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function submit(&$group, $form, $npath) {
    $button_delete = $group->child_select('button_delete');
    if ($button_delete->is_clicked()) {
      $fields = $form->validation_cache_get('fields');
      foreach ($fields as $c_row_id => $c_field) {
        if ($c_field->entity_name       == $group->entity_name &&
            $c_field->entity_field_name == $group->entity_field_name) {
          unset($fields[$c_row_id]);
          $form->validation_cache_is_persistent = true;
          $form->validation_cache_set('fields', $fields);
          $entity = entity::get($group->entity_name);
          $entity_field = $entity ? $entity->field_get($group->entity_field_name) : null;
          message::insert(new text('Field "%%_title" (%%_id) was deleted.', ['title' => isset($entity_field->title) ? translation::get($entity->title).': '.translation::get($entity_field->title) : 'LOST PART', 'id' => $group->entity_name.'.'.$group->entity_field_name]));
          message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
          return true;
        }
      }
    }
  }

}}