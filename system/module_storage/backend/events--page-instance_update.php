<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\access;
          use \effcore\core;
          use \effcore\entity;
          abstract class events_page_instance_update {

  static function on_check_access($event, $page) {
    $entity_name = $page->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if (isset($entity->access_update) && !access::check(
                $entity->access_update))
           core::send_header_and_exit('access_forbidden');
    } else core::send_header_and_exit('page_not_found');
  }

}}