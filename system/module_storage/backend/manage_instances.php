<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class manage_instances {

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
