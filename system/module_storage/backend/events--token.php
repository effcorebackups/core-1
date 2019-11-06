<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\translation;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    switch ($name) {
      case 'instance_id_context' : return page::get_current()->args_get('instance_id');
      case 'entity_name_context' : return page::get_current()->args_get('entity_name');
      case 'entity_title_context':
        $entity_name = page::get_current()->args_get('entity_name');
        $entity = entity::get($entity_name, false);
        return $entity ? translation::get($entity->title) : '';
      case 'entity_title_plural_context':
        $entity_name = page::get_current()->args_get('entity_name');
        $entity = entity::get($entity_name, false);
        return $entity ? translation::get($entity->title_plural) : '';
    };
    return '';
  }

}}