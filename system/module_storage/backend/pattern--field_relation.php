<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_relation extends field_select {

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
      $this->child_select('element')->children_delete();
      $this->option_insert($this->title__not_selected, 'not_selected');
      $entity = entity::get($this->related_entity_name);
      $instances = $entity->instances_select($this->query_settings);
      if ($this->related_entity_field_id_parent_name) {
        $tree_id = 'field_relation-'.$this->name_get();
                tree::delete(      $tree_id);
        $tree = tree::insert(null, $tree_id, null, [], 0, 'storage');
        foreach ($instances as $c_instance) {
          $c_tree_item = tree_item::insert(
            $c_instance->{$this->related_entity_field_title_name    },           $tree_id.'-'.
            $c_instance->{$this->related_entity_field_id_name       },
            $c_instance->{$this->related_entity_field_id_parent_name} !== null ? $tree_id.'-'.
            $c_instance->{$this->related_entity_field_id_parent_name} :   null,  $tree_id, null, null, [], [], $c_instance->weight, 'storage');
          $c_tree_item->id_real = $c_instance->{$this->related_entity_field_id_name};
        }
        $tree->build();
        foreach ($tree->children_select_recursive(null, '', false, true) as $c_npath => $c_child) {
          $c_depth = core::path_get_depth($c_npath);
          if ($this->title_ws_id === true) $this->option_insert(str_repeat('—', $c_depth + 1).' '.(new text($c_child->title))->render().' ('.$c_child->id_real.')', $c_child->id_real);
          if ($this->title_ws_id !== true) $this->option_insert(str_repeat('—', $c_depth + 1).' '.(new text($c_child->title))->render(),                            $c_child->id_real);
        }
      } else {
        foreach ($instances as $c_instance) {
          if ($this->title_ws_id === true) $this->option_insert( (new text($c_instance->{$this->related_entity_field_title_name}))->render().' ('.$c_instance->{$this->related_entity_field_id_name}.')', $c_instance->{$this->related_entity_field_id_name});
          if ($this->title_ws_id !== true) $this->option_insert(           $c_instance->{$this->related_entity_field_title_name},                                                                         $c_instance->{$this->related_entity_field_id_name});
        }
      }
      $this->is_builded = true;
    }
  }

}}