<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\br;
          use \effcore\access;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\response;
          use \effcore\text_multiline;
          abstract class events_page_instance_delete {

  static function on_check_existence($event, $page) {
    $managing_group_id = $page->args_get('managing_group_id');
    $entity_name       = $page->args_get('entity_name');
    $instance_id       = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    $groups = entity::get_managing_group_ids();
    if ($managing_group_id === null || isset($groups[$managing_group_id])) {
      if ($entity) {
        if ($entity->managing_is_enabled) {
          $id_keys   = $entity->id_get();
          $id_values = explode('+', $instance_id);
          if (count($id_keys) ==
              count($id_values)) {
            $conditions = array_combine($id_keys, $id_values);
            $instance = new instance($entity_name, $conditions);
            if ($instance->select()) {
              return true;
            } else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong instance key',                          'go to <a href="/">front page</a>'], [], br.br));
          }   else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong instance keys',                         'go to <a href="/">front page</a>'], [], br.br));
        }     else response::send_header_and_exit('page_not_found', null, new text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], br.br));
      }       else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], br.br));
    }         else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], br.br));
  }

  static function on_check_access($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if (!access::check($entity->access_delete)) {
      response::send_header_and_exit('access_forbidden');
    }
    $id_keys = $entity->id_get();
    $id_values = explode('+', $instance_id);
    $conditions = array_combine($id_keys, $id_values);
    $instance = new instance($entity_name, $conditions);
    if ($instance->select() && !empty($instance->is_embedded)) {
      response::send_header_and_exit('access_forbidden', null, new text_multiline(['entity is embedded', 'go to <a href="/">front page</a>'], [], br.br));
    }
  }

}}