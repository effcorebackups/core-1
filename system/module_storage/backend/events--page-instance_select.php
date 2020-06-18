<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\access;
          use \effcore\actions_list;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\selection;
          use \effcore\url;
          abstract class events_page_instance_select {

  static function on_redirect_and_check_existence($event, $page) {
    $managing_group_id = $page->args_get('managing_group_id');
    $entity_name       = $page->args_get('entity_name');
    $instance_id       = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    $groups = entity::get_managing_group_ids();
    if ($entity) {
      if (isset($groups[$managing_group_id])) {
        $id_keys   = $entity->id_get_real();
        $id_values = explode('+', $instance_id);
        if (count($id_keys) ==
            count($id_values)) {
          $conditions = array_combine($id_keys, $id_values);
          $instance = new instance($entity_name, $conditions);
          if ($instance->select() == null && url::back_url_get() != '') url::go(url::back_url_get()); # after deletion
          if ($instance->select() == null && url::back_url_get() == '')
               core::send_header_and_exit('page_not_found');
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_check_access($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if (isset($entity->access_select) && !access::check(
                $entity->access_select))
           core::send_header_and_exit('access_forbidden');
    } else core::send_header_and_exit('page_not_found');
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
          foreach ($entity->selection_params_for_managing as $c_key => $c_value)
            $selection                                    ->{$c_key} = $c_value;
            $selection->query_params['conditions'] = $entity->storage_get()->attributes_prepare($conditions);
          if (empty($selection->decorator_params['view_type']))
                    $selection->decorator_params['view_type'] = 'ul';
          $has_visible_fields = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_select_is_enabled)) {
              $has_visible_fields = true;
              $selection->field_insert_entity(null,
                $entity->name, $c_name, $c_field->selection_params_default ?? []
              );
            }
          }
          if (!$has_visible_fields) {
            return new markup('x-no-items', ['data-style' => 'table'], 'no fields');
          } else {
            $selection->field_insert_code('actions', 'Actions', function ($c_row, $c_instance) {
              $c_actions_list = new actions_list;
              if (true && empty($c_instance->is_embed)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.url::back_part_make(), 'delete');
              if (true                                ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.url::back_part_make(), 'update');
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