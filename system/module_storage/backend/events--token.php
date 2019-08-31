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
      case 'instance_id_context': return page::get_current()->args_get('instance_id');
      case 'entity_name_context': return page::get_current()->args_get('entity_name');
      case 'entity_title_page_context':
      case 'entity_title_plural_page_context':
        if (isset($args[0])) {
          $entity_name = page::get_current()->args_get($args[0]);
          $entities = entity::get_all(false);
          if (isset($entities[$entity_name])) {
            if ($name == 'entity_title_page_context'       ) return translation::get($entities[$entity_name]->title       );
            if ($name == 'entity_title_plural_page_context') return translation::get($entities[$entity_name]->title_plural);
          }
        }
        break;
    };
    return '';
  }

}}