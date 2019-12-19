<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_field_manage extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-rearrangeable' => 'true', 'data-fields-is-inline-full' => 'true'];
  public $on_click_delete_handler;
  public $entity_name;
  public $entity_field_name;

  function __construct($entity_name, $entity_field_name, $attributes = [], $weight = 0) {
    $this->entity_name       = $entity_name;
    $this->entity_field_name = $entity_field_name;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

 function build() {
    if (!$this->is_builded) {
      $field_name_suffix = $this->entity_name.'_'.$this->entity_field_name;
      $entity = entity::get($this->entity_name);
      $entity_field = $entity ? $entity->field_get($this->entity_field_name) : null;
      $this->weight = (int)(field::request_value_get('weight_'.$field_name_suffix) !== '' ?
                            field::request_value_get('weight_'.$field_name_suffix) : $this->weight);
      $field_weight = new field_weight();
      $field_weight->description_state = 'hidden';
      $field_weight->build();
      $field_weight->name_set('weight_'.$field_name_suffix);
      $field_weight->required_set(false);
      $field_weight->value_set($this->weight);
      $button_delete = new button('', ['data-style' => 'narrow-delete', 'title' => new text('delete')]);
      $button_delete->build();
      $button_delete->value_set('button_field_delete_'.$field_name_suffix);
      $this->child_insert($field_weight,  'field_weight' );
      $this->child_insert($button_delete, 'button_delete');
      $this->child_insert(new markup('x-info', [], [
        'title' => new markup('x-title', [], isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST PART'),
        'id'    => new markup('x-id',    [], [
                   new text_simple($this->entity_name      ), '.',
                   new text_simple($this->entity_field_name)]) ]), 'info');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_submit(&$group, $form, $npath) {
    $button_delete = $group->child_select('button_delete');
    if ($button_delete->is_clicked()) {
      if ($group->on_click_delete_handler) {
        return call_user_func($group->on_click_delete_handler, $group, $form, $npath);
      }
    }
  }

}}