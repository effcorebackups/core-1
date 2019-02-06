<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_instances {

  # function() ←→ url mapping:
  # ─────────────────────────────────────────────────┬─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
  # instance_select_multiple_by_entity_name()        │ /manage/instances/action_select → /manage/instances/action_select/%%_entity_name
  # instance_insert_by_entity_name()                 │ /manage/instances/action_insert → /manage/instances/action_insert/%%_entity_name
  # instance_select_by_entity_name_and_instance_id() │                                   /manage/instances/action_select/%%_entity_name/%%_instance_id
  # instance_update_by_entity_name_and_instance_id() │                                   /manage/instances/action_update/%%_entity_name/%%_instance_id
  # instance_delete_by_entity_name_and_instance_id() │                                   /manage/instances/action_delete/%%_entity_name/%%_instance_id
  # instance_delete_multiple_by_instances_id()       │                                   /manage/instances/action_delete/%%_entity_name/%%_instance_id_1/…/%%_instance_id_N
  # ─────────────────────────────────────────────────┴─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────

  # ─────────────────────────────────────────────────────────────────────
  # select instances and single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_select_multiple_by_entity_name($page) {
    $entities = entity::all_get(false);
    $entity_name = $page->args_get('entity_name');  
    core::array_sort_by_property($entities, 'title');
    if (!isset($entities[$entity_name])) url::go($page->args_get('base').'/select/'.reset($entities)->name);
    foreach ($entities as $c_entity) {
      tabs::item_insert(             $c_entity->title_plural,
        'instance_select_'.          $c_entity->name,
        'instance_select', 'select/'.$c_entity->name, null, ['class' => [
                           'select-'.$c_entity->name =>
                           'select-'.$c_entity->name]]
      );
    }
  # create selection
    $entity = entity::get($entity_name);
    if ($entity) {
      $selection = new selection;
      foreach ($entity->fields as $c_name => $c_info) {
        if (!empty($c_info->show_in_manager)) {
          $selection->field_entity_insert($entity->name, $c_name);
        }
      }
      $selection->field_action_insert();
      $markup = $selection->build();
      return new block('', ['class' => [
        $entity->name =>
        $entity->name]],
        $markup
      );
    }
  }

  static function instance_select_by_entity_name_and_instance_id($page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = entity::get($entity_name)->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $storage = storage::get(entity::get($entity_name)->storage_name);
        $conditions = array_combine($id_keys, $id_values);
        $conditions_pure = $storage->pure_conditions($conditions);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
        # create selection
          $selection = new selection;
          $selection->title = '';
          $selection->view_type = 'list';
          $selection->pure_conditions = $conditions_pure;
          foreach ($entity->fields as $c_name => $c_info) {
            if (!empty($c_info->show_in_manager)) {
              $selection->field_entity_insert($entity->name, $c_name);
            }
          }
          $selection->field_action_insert();
          $markup = $selection->build();
          return new block('', ['class' => [
            $entity->name =>
            $entity->name]],
            $markup
          );
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    } else {
      url::go(
        $page->args_get('base').'/select'
      );
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # insert single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_insert_by_entity_name($page) {
    $entities = entity::all_get(false);
    $entity_name = $page->args_get('entity_name');
    core::array_sort_by_property($entities, 'title');
    if (!isset($entities[$entity_name])) url::go($page->args_get('base').'/insert/'.reset($entities)->name);
    foreach ($entities as $c_entity) {
      tabs::item_insert(             $c_entity->title,
        'instance_insert_'.          $c_entity->name,
        'instance_insert', 'insert/'.$c_entity->name, null, ['class' => [
                           'insert-'.$c_entity->name =>
                           'insert-'.$c_entity->name]]
      );
    }
  # create insert form
    $entity = entity::get($entity_name);
    if ($entity) {
    # @todo: make functionality
      return new text('instance_insert is UNDER CONSTRUCTION');
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # update single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_update_by_entity_name_and_instance_id($page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # @todo: make functionality
      return new text('instance_update is UNDER CONSTRUCTION');
    } else {
      url::go(
        $page->args_get('base').'/select'
      );
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # delete single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_delete_by_entity_name_and_instance_id($page, $form, $items) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = entity::get($entity_name)->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $instance = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($instance->select()) {
          return $instance->delete();
        }
      }
    }
  }

  static function instance_delete_multiple_by_instances_id($page) {
  # @todo: make functionality
    return new text('instances_delete is UNDER CONSTRUCTION');
  }

}}
