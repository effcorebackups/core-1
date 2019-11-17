<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\actions_list;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\selection;
          use \effcore\url;
          abstract class events_page_instance_select {

  static function on_build_before($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->id_get_real();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $conditions = array_combine($id_keys, $id_values);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function block_instance_select($page, $args) {
                   $page->args_set('action_name', 'select');
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->id_get_real();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $conditions = array_combine($id_keys, $id_values);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
          $selection = new selection;
          $selection->id = 'instance_select-'.$entity->name;
          foreach ($entity->managing_selection_params as $c_key => $c_value)
            $selection                                ->{$c_key} = $c_value;
            $selection->decorator_params['view_type'] = $entity->decorator_view_type_single;
            $selection->query_params[   'conditions'] = $entity->storage_get()->attributes_prepare($conditions);
          $has_visible_fields = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_is_on_select)) {
              $has_visible_fields = true;
              $selection->field_insert_entity(null,
                $entity->name, $c_name, $c_field->managing_selection_params ?? []
              );
            }
          }
          if (!$has_visible_fields) {
            return new markup('x-no-result', [], 'no visible fields');
          } else {
            $selection->field_insert_code('actions', 'Actions', function ($c_row, $c_instance) {
              $c_actions_list = new actions_list();
              if (true && empty($c_instance->is_embed)) $c_actions_list->action_insert('/manage/data/'.$c_instance->entity_get()->group_managing_get_id().'/'.$c_instance->entity_get()->name.'/'.join('+', $c_instance->values_id_get()).'/delete?'.url::back_part_make(), 'delete');
              if (true                                ) $c_actions_list->action_insert('/manage/data/'.$c_instance->entity_get()->group_managing_get_id().'/'.$c_instance->entity_get()->name.'/'.join('+', $c_instance->values_id_get()).'/update?'.url::back_part_make(), 'update');
              return $c_actions_list;
            });
            $selection->build();
            return $selection;
          }
        }
      }
    }
  }

}}