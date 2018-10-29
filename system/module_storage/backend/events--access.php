<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          abstract class events_access {

  static function on_check_access_instance_select($page) {}

  static function on_check_access_instance_insert($page) {}

  static function on_check_access_instance_update($page) {}

  static function on_check_access_instance_delete($page) {
    $entity_name = $page->args_get('entity_name');
    $instance_id = $page->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = entity::get($entity_name)->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $instance = new instance($entity_name, array_combine($id_keys, $id_values));
        $result = $instance->select();
        if (!empty($result->is_embed)) core::send_header_and_exit('access_forbidden');
        if (!      $result)            core::send_header_and_exit('page_not_found');
      } else                           core::send_header_and_exit('page_not_found');
    }   else                           core::send_header_and_exit('page_not_found');
  }

}}