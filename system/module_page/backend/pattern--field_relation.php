<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_relation extends field_select {

  public $attributes = ['data-type' => 'relation'];
  public $element_attributes = [
    'name'     => 'relation',
    'required' => true];
# ─────────────────────────────────────────────────────────────────────
  public $entity_name;
  public $entity_field_id_name;
  public $entity_field_id_parent_name;
  public $entity_field_title_name;
  public $query_params = [];

  function build() {
    if (!$this->is_builded) {
         $this->is_builded = true;
      parent::build();
      $this->option_insert('- no -', 'not_selected');
      $entity = entity::get($this->entity_name);
      $instances = $entity->instances_select($this->query_params);
      if ($this->entity_field_id_parent_name) {
        $tree_id = 'field_relation-'.$this->name_get();
        $tree = tree::insert('', $tree_id);
        foreach ($instances as $c_instance) {
          $c_tree_item = tree_item::insert(
            $c_instance->{$this->entity_field_title_name    },           $tree_id.'-'.
            $c_instance->{$this->entity_field_id_name       },
            $c_instance->{$this->entity_field_id_parent_name} !== null ? $tree_id.'-'.
            $c_instance->{$this->entity_field_id_parent_name} :   null,  $tree_id, null, null, [], [], $c_instance->weight);
          $c_tree_item->id_real = $c_instance->{$this->entity_field_id_name};
        }
        $tree->build();
        foreach ($tree->children_select_recursive() as $c_npath => $c_child) {
          $c_depth = count_chars($c_npath, 1)[ord('/')] ?? 0;
          $this->option_insert(str_repeat('- ', $c_depth + 1).
            $c_child->title,
            $c_child->id_real
          );
        }
      } else {
        foreach ($instances as $c_instance) {
          $this->option_insert(
            $c_instance->{$this->entity_field_title_name},
            $c_instance->{$this->entity_field_id_name   }
          );
        }
      }
    }
  }

}}