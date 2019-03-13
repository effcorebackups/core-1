<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_instances {

  # function() ←→ url mapping:
  # ────────────────────────────┬──────────────────────────────────────────────────────────────────────────────────
  # instance_select_multiple()  │ /manage/instances/select → /manage/instances/select/%%_entity_name
  # instance_insert()           │ /manage/instances/insert → /manage/instances/insert/%%_entity_name
  # instance_select()           │                            /manage/instances/select/%%_entity_name/%%_instance_id
  # events_form_instance_update │                            /manage/instances/update/%%_entity_name/%%_instance_id
  # instance_delete()           │                            /manage/instances/delete/%%_entity_name/%%_instance_id
  # ────────────────────────────┴──────────────────────────────────────────────────────────────────────────────────

  # ─────────────────────────────────────────────────────────────────────
  # select multiple instances
  # ─────────────────────────────────────────────────────────────────────

  static function instance_select_multiple($page) {
    $entities = entity::all_get(false);
    $entity_name = $page->args_get('entity_name');  
    core::array_sort_by_title($entities);
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
      $selection->is_paged = true;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->show_in_manager)) {
          $selection->field_entity_insert(null, $entity->name, $c_name);
        }
      }
      $selection->field_checkbox_insert(null, '', 80);
      $selection->field_action_insert();
      return new block('', ['class' => [
        $entity->name =>
        $entity->name]],
        $selection
      );
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # select single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_select($page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $storage = storage::get($entity->storage_name);
        $conditions = array_combine($id_keys, $id_values);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
        # create selection
          $selection = new selection('', 'ul');
          $selection->query_params['conditions'] = $storage->attributes_prepare($conditions);
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->show_in_manager)) {
              $selection->field_entity_insert(null, $entity->name, $c_name);
            }
          }
          $selection->field_action_insert(null, 'Action');
          return new block('', ['class' => [
            $entity->name =>
            $entity->name]],
            $selection
          );
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  # ─────────────────────────────────────────────────────────────────────
  # insert single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_insert($page, $emulate = true) {
    $entities = entity::all_get(false);
    $entity_name = $page->args_get('entity_name');
    core::array_sort_by_title($entities);
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
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # delete single instance
  # ─────────────────────────────────────────────────────────────────────

  static function instance_delete($page, $emulate = true) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $instance = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($instance->select()) {
          if (!empty($instance->is_embed)) core::send_header_and_exit('access_forbidden');
          if (!$emulate) return $instance->delete();
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  # ─────────────────────────────────────────────────────────────────────
  # delete multiple instances
  # ─────────────────────────────────────────────────────────────────────

  static function instance_delete_multiple($page) {
  # @todo: make functionality
    return new text('instances_delete is UNDER CONSTRUCTION');
  }

}}
