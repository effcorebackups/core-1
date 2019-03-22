<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\block;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\selection;
          use \effcore\storage;
          use \effcore\tabs;
          use \effcore\text;
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
    $entities = entity::all_get();
    $groups = entity::groups_get();
    $entities_by_groups = [];
    core::array_sort_text($groups);
    foreach ($groups as $c_id => $c_title) {
      foreach ($entities as $c_name => $c_entity)
        if ($c_id == $c_entity->group_id_get())
          $entities_by_groups[$c_id][$c_name] = $c_entity;
      core::array_sort_by_title(
        $entities_by_groups[$c_id]
      );
    }
  # ┌───────────────────────────────────────────────────────────┬─────────────────────────────────────────┐
  # │ /manage/instances/select                                  │ group_id != true && entity_name != true │
  # │ /manage/instances/select/      group_id                   │ group_id == true && entity_name != true │
  # │ /manage/instances/select/      group_id/      entity_name │ group_id == true && entity_name == true │
  # │ /manage/instances/select/wrong_group_id                   │ group_id != true && entity_name != true │
  # │ /manage/instances/select/wrong_group_id/      entity_name │ group_id != true && entity_name == true │
  # │ /manage/instances/select/      group_id/wrong_entity_name │ group_id == true && entity_name != true │
  # │ /manage/instances/select/wrong_group_id/wrong_entity_name │ group_id != true && entity_name != true │
  # └───────────────────────────────────────────────────────────┴─────────────────────────────────────────┘
    if (isset($groups[$group_id])                                                        == false) url::go($page->args_get('base').'/'.array_keys($groups)[0].'/'.array_keys($entities_by_groups[array_keys($groups)[0]])[0]);
    if (isset($groups[$group_id]) && isset($entities_by_groups[$group_id][$entity_name]) == false) url::go($page->args_get('base').'/'.           $group_id  .'/'.array_keys($entities_by_groups[           $group_id  ])[0]);
  # make tabs
    foreach ($entities_by_groups as $c_id => $c_entities) {
      tabs::item_insert($groups[$c_id],
              'instance_group_'.$c_id,
          'T:manage_instances', $c_id, null, ['class' => [
                       'group-'.$c_id =>
                       'group-'.$c_id]]);
      foreach ($c_entities as $c_name =>  $c_entity) {
        tabs::item_insert(      $c_entity->title_plural,
             'instance_select_'.$c_name,
              'instance_group_'.$c_id, $c_id.'/'.$c_name, null, ['class' => [
                      'select-'.$c_name =>
                      'select-'.$c_name]]);
      }
    }
  }

  static function on_show_block_instance_select_multiple($page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    $link_add_new = new markup('a', ['href' => '/manage/instance/insert/'.$entity_name.'?'.url::back_part_make(), 'class' => [
      'like-button'           => 'like-button',
      'link-add-new-instance' => 'link-add-new-instance']], new text('add'));
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
      return new block('', ['class' => [$entity->name => $entity->name]], [
        $link_add_new,
        $selection
      ]);
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