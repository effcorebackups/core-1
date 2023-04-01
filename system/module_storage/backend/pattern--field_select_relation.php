<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_select_relation extends field_select {

  public $title = 'Relation';
  public $title_ws_id = true;
  public $title__not_selected = '- select -';
  public $attributes = ['data-type' => 'relation'];
  public $element_attributes = [
    'name'     => 'relation',
    'required' => true];
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $related_entity_name;
  public $related_entity_field_id_name;
  public $related_entity_field_id_parent_name;
  public $related_entity_field_title_name;
  public $query_settings = [];

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $entity = entity::get($this->related_entity_name);
      if ($entity) {
        $this->_instances = $entity->instances_select($this->query_settings);
        if ($this->related_entity_field_id_parent_name)
             $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate_tree($this);
        else $this->items = ['not_selected' => $this->title__not_selected] + static::items_generate_flat($this);
        $this->is_builded = false;
        parent::build();
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function items_generate_flat($field) {
    $result = [];
    foreach ($field->_instances as $c_instance) {
      $c_id_real = $c_instance->{$field->related_entity_field_id_name};
      if ($field->title_ws_id === true) $result[$c_id_real] = new text_multiline(['title' => $c_instance->{$field->related_entity_field_title_name}, 'id' => '('.$c_id_real.')'], [], ' ');
      if ($field->title_ws_id !== true) $result[$c_id_real] = new text_multiline(['title' => $c_instance->{$field->related_entity_field_title_name}                            ], [], ' ');
    }
    return $result;
  }

  static function items_generate_tree($field) {
    $tree_id = 'field_select_relation-'.$field->name_get();
            tree::delete(      $tree_id                        );
    $tree = tree::insert(null, $tree_id, null, [], 0, 'storage');
    foreach ($field->_instances as $c_instance) {
      $c_tree_item = tree_item::insert(
        $c_instance->{$field->related_entity_field_title_name    },           $tree_id.'-'.
        $c_instance->{$field->related_entity_field_id_name       },
        $c_instance->{$field->related_entity_field_id_parent_name} !== null ? $tree_id.'-'.
        $c_instance->{$field->related_entity_field_id_parent_name} :   null,  $tree_id, null, null, [], [], $c_instance->weight, 'storage');
      $c_tree_item->id_real = $c_instance->{$field->related_entity_field_id_name};
    }
    $tree->build();
    $result = [];
    foreach ($tree->children_select_recursive(null, '', false, true) as $c_npath => $c_child) {
      $c_id_real = $c_child->id_real;
      $c_depth_marker = str_repeat('—', core::path_get_depth($c_npath) + 1);
      if ($field->title_ws_id === true) $result[$c_id_real] = new text_multiline(['depth_marker' => $c_depth_marker, 'title' => $c_child->title, 'id' => '('.$c_id_real.')'], [], ' ');
      if ($field->title_ws_id !== true) $result[$c_id_real] = new text_multiline(['depth_marker' => $c_depth_marker, 'title' => $c_child->title                            ], [], ' ');
    }
    return $result;
  }

}}