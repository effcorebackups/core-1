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
          abstract class events_page_instance_select {

  static function on_page_init($page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->get_real_id();
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
      $id_keys   = $entity->get_real_id();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $storage = storage::get($entity->storage_name);
        $conditions = array_combine($id_keys, $id_values);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
        # create selection
          $selection = new selection('', $entity->view_type_single);
          $selection->id = 'instance_manage';
          $selection->query_params['conditions'] = $storage->attributes_prepare($conditions);
          foreach ($entity->selection_params as $c_key => $c_value) {
            $selection->{$c_key} = $c_value;
          }
          $has_visible_fields = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->field_is_visible_on_select)) {
              $has_visible_fields = true;
              $selection->field_insert_entity(null, $entity->name, $c_name);
            }
          }
          if (!$has_visible_fields) {
            return new block('', ['class' => [$entity->name => $entity->name]],
              new markup('x-no-result', [], 'no visible fields')
            );
          } else {
            $selection->field_insert_action(null, 'Action');
            return new block('', ['class' => [$entity->name => $entity->name]],
              $selection
            );
          }
        }
      }
    }
  }

}}