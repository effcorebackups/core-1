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
      $idkeys = entity::get($entity_name)->real_id_get();
      $idvalues = explode('+', $instance_id);
      if (count($idkeys) ==
          count($idvalues)) {
        $instance = new instance($entity_name, array_combine($idkeys, $idvalues));
        $result = $instance->select();
        if (!empty($result->is_embed)) core::send_header_and_exit('access_denided');
        if (!      $result)            core::send_header_and_exit('page_not_found');
      } else                           core::send_header_and_exit('page_not_found');
    }   else                           core::send_header_and_exit('page_not_found');
  }

}}