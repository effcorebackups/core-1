<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\br;
          use \effcore\access;
          use \effcore\actions_list;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\response;
          use \effcore\selection;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\token;
          use \effcore\url;
          abstract class events_page_instance_select {

  static function on_redirect_and_check_existence($event, $page) {
    $managing_group_id = $page->args_get('managing_group_id');
    $entity_name       = $page->args_get('entity_name');
    $instance_id       = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    $groups = entity::get_managing_group_ids();
    if (isset($groups[$managing_group_id])) {
      if ($entity) {
        if ($entity->managing_is_enabled) {
          $id_keys   = $entity->id_get();
          $id_values = explode('+', $instance_id);
          if (count($id_keys) ===
              count($id_values)) {
            $conditions = array_combine($id_keys, $id_values);
            $instance = new instance($entity_name, $conditions);
            if ($instance->select() === null && url::back_url_get() !== '') url::go(url::back_url_get()); # after deletion
            if ($instance->select() === null && url::back_url_get() === '')
                 response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong instance key',                          'go to <a href="/">front page</a>'], [], br.br));
          } else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong number of instance keys',               'go to <a href="/">front page</a>'], [], br.br));
        }   else response::send_header_and_exit('page_not_found', null, new text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], br.br));
      }     else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], br.br));
    }       else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], br.br));
  }

  static function on_check_access($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    if (!access::check($entity->access_select)) {
      response::send_header_and_exit('access_forbidden');
    }
  }

  static function block_markup__instance_select($page, $args = []) {
                   $page->args_set('action_name', 'select');
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ===
          count($id_values)) {
        $conditions = array_combine($id_keys, $id_values);
        $instance = new instance($entity_name, $conditions);
        if ($instance->select()) {
          $selection = selection::get('instance_select-'.$entity->name);
          if ($selection) {
            foreach ($conditions as $c_id_key => $c_id_value)
              token::insert('selection_'.$entity_name.'_'.$c_id_key.'_context', 'text', $c_id_value, null, 'storage');
            $selection = core::deep_clone($selection);
            $has_access_update = access::check($entity->access_update);
            $has_access_delete = access::check($entity->access_delete);
            if ($has_access_update ||
                $has_access_delete) {
              $selection->fields['code']['actions'] = new \stdClass;
              $selection->fields['code']['actions']->title = 'Actions';
              $selection->fields['code']['actions']->weight = -500;
              $selection->fields['code']['actions']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) use ($has_access_update, $has_access_delete) {
                $c_actions_list = new actions_list;
                if ($has_access_delete && empty($c_instance->is_embedded)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.url::back_part_make(), 'delete');
                if ($has_access_update                                   ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.url::back_part_make(), 'update');
                return $c_actions_list;
              };
            }
            $selection->build();
            return $selection;
          } else {
            return new markup('x-no-items', ['data-style' => 'table'], new text(
              'No Selection with ID = "%%_id".', ['id' => 'instance_select-'.$entity->name]
            ));
          }
        }
      }
    }
  }

}}