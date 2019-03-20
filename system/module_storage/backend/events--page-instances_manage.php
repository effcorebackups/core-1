<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\selection;
          use \effcore\storage;
          use \effcore\tabs;
          use \effcore\url;
          abstract class events_page_instances_manage {

  # URLs for manage:
  # ─────────────────────────────────────────────────────────────────────────────────
  # /manage/instances/select → /manage/instances/select/%%_entity_name
  # /manage/instances/insert → /manage/instances/insert/%%_entity_name
  #                            /manage/instances/select/%%_entity_name/%%_instance_id
  #                            /manage/instances/update/%%_entity_name/%%_instance_id
  #                            /manage/instances/delete/%%_entity_name/%%_instance_id
  # ─────────────────────────────────────────────────────────────────────────────────

  # ─────────────────────────────────────────────────────────────────────
  # select multiple instances
  # ─────────────────────────────────────────────────────────────────────

  static function on_page_instance_select_multiple_init($page) {
    $group_id = $page->args_get('group_id');
    $entity_name = $page->args_get('entity_name');
    $entities = entity::all_get(false);
    $entities_groups = entity::groups_get();
    $entities_by_groups = [];
    foreach ($entities as $c_entity) {
      $c_group_id = core::sanitize_id($c_entity->group);
      $entities_by_groups[$c_group_id][$c_entity->name] = $c_entity;
    }
    foreach ($entities_by_groups as &$c_entities)
      ksort($c_entities);
      ksort($entities_groups);
      ksort($entities_by_groups);
  # ┌───────────────────────────────────────────────────────────┬─────────────────────────────────────────┬─────────────┐
  # │ /manage/instances/select                                  │ group_id != true && entity_name != true │ redirect #1 │
  # │ /manage/instances/select/      group_id                   │ group_id == true && entity_name != true │ redirect #2 │
  # │ /manage/instances/select/      group_id/      entity_name │ group_id == true && entity_name == true │ true        │
  # │ /manage/instances/select/wrong_group_id                   │ group_id != true && entity_name != true │ redirect #1 │
  # │ /manage/instances/select/wrong_group_id/      entity_name │ group_id != true && entity_name == true │ redirect #1 │
  # │ /manage/instances/select/      group_id/wrong_entity_name │ group_id == true && entity_name != true │ redirect #2 │
  # │ /manage/instances/select/wrong_group_id/wrong_entity_name │ group_id != true && entity_name != true │ redirect #1 │
  # └───────────────────────────────────────────────────────────┴─────────────────────────────────────────┴─────────────┘
    if (!isset($entities_groups[$group_id   ])) url::go($page->args_get('base').'/'.array_keys($entities_groups)[0].'/'.array_keys($entities_by_groups[$group_id])[0]); # redirect 1
    if (!isset($entities       [$entity_name])) url::go($page->args_get('base').'/'.           $group_id           .'/'.array_keys($entities_by_groups[$group_id])[0]); # redirect 2
  # make tabs
    foreach ($entities_by_groups as $c_group_id => $c_entities) {
      tabs::item_insert($entities_groups[$c_group_id],
                       'instance_group_'.$c_group_id,
                   'T:manage_instances', $c_group_id, null, ['class' => [
                                'group-'.$c_group_id =>
                                'group-'.$c_group_id]]);
      foreach ($c_entities as $c_entity) {
        tabs::item_insert(                 $c_entity->title_plural,
                        'instance_select_'.$c_entity->name,
            'instance_group_'.$c_group_id, $c_group_id.'/'.$c_entity->name, null, ['class' => [
                                 'select-'.$c_entity->name =>
                                 'select-'.$c_entity->name]]);
      }
    }
  }

  static function on_show_block_instance_select_multiple($page) {
    $entity_name = $page->args_get('entity_name');
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

  static function on_page_instance_select_init($page) {
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
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_show_block_instance_select($page) {
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
        }
      }
    }
  }

}}