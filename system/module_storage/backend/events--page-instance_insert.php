<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\br;
          use \effcore\access;
          use \effcore\entity;
          use \effcore\response;
          use \effcore\text_multiline;
          abstract class events_page_instance_insert {

  static function on_check_existence($event, $page) {
    $managing_group_id = $page->args_get('managing_group_id');
    $entity_name       = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    $groups = entity::get_managing_group_ids();
    if ($managing_group_id === null || isset($groups[$managing_group_id])) {
      if ($entity) {
        if ($entity->managing_is_enabled) {
          return true;
        } else response::send_header_and_exit('page_not_found', null, new text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], br.br));
      }   else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], br.br));
    }     else response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], br.br));
  }

  static function on_check_access($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    if (!access::check($entity->access_insert)) {
      response::send_header_and_exit('access_forbidden');
    }
  }

}}