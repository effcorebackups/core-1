<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\field_hidden;
          use \effcore\field;
          use \effcore\message;
          use \effcore\node;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_instance_select_multiple {

  static function on_init($form, $items) { # drag-and-drop functionality
    $entity_name = page::get_current()->args_get('entity_name'       );
    $id_tree     = page::get_current()->args_get('instances_group_by');
    if ($entity_name == 'tree_item' && $id_tree && !empty($form->_selection)) {
      $items['#actions']->disabled_set();
      $form->_selection->is_builded = false;
      $form->_selection->query_params['conditions'] = ['id_tree_!f' => 'id_tree', 'operator' => '=', 'id_tree_!v' => $id_tree];
      $form->_selection->field_insert_action(null, '', ['delete', 'select', 'update']);
      $form->_selection->field_insert_code('extra', '', function($c_row, $c_instance){
        $c_hidden_parent = new field_hidden('parent-'.$c_instance->id, $c_instance->id_parent, ['data-parent' => 'true']);
        $c_hidden_weight = new field_hidden('weight-'.$c_instance->id, $c_instance->weight,    ['data-weight' => 'true']);
        return new node([], [
          'actions'       => $c_row['actions']['value'],
          'hidden_parent' => $c_hidden_parent,
          'hidden_weight' => $c_hidden_weight]);});
      $form->_selection->build();
      if (!count($form->_selection->_instances)) {
        $items['~apply']->disabled_set();
      }
    }
  }

  static function on_submit($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name'       );
    $id_tree     = page::get_current()->args_get('instances_group_by');
    $entity = entity::get($entity_name);
    if ($entity_name == 'tree_item' && $id_tree) {
      $has_selection = false;
      $tree_items = entity::get('tree_item')->instances_select(['conditions' => ['id_tree_!f' => 'id_tree', 'operator' => '=', 'id_tree_!v' => $id_tree]], 'id');
      foreach ($tree_items as $c_item) {
        $c_new_parent = field::request_value_get('parent-'.$c_item->id) ?: null;
        $c_new_weight = field::request_value_get('weight-'.$c_item->id) ?: 0;
        if ($c_new_parent == null || isset($tree_items[$c_new_parent])) {
          if ($c_item->id_parent != $c_new_parent ||
              $c_item->weight    != $c_new_weight) {
              $c_item->id_parent  = $c_new_parent;
              $c_item->weight     = $c_new_weight;
              $has_selection      = true;
            if ($c_item->update()) message::insert(new text('Item of type "%%_name" with id = "%%_id" was updated.',     ['name' => translation::get($entity->title), 'id' => $c_item->id])           );
            else                   message::insert(new text('Item of type "%%_name" with id = "%%_id" was not updated!', ['name' => translation::get($entity->title), 'id' => $c_item->id]), 'warning');
          }
        }
      }
      if (!$has_selection) {
        message::insert(
          'Nothing selected!', 'warning'
        );
      }
      static::on_init($form, $items);
    }
  }

}}