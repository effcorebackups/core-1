<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\text;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'instance_id_context'        : if (page::get_current()) {return         page::get_current()->args_get('instance_id');} break;
      case 'entity_name_context'        : if (page::get_current()) {return         page::get_current()->args_get('entity_name');} break;
      case 'entity_title_context'       : if (page::get_current()) {$entity_name = page::get_current()->args_get('entity_name'); if ($entity_name) {$entity = entity::get($entity_name, false); if ($entity) return (new text($entity->title       ))->render();}} break;
      case 'entity_title_plural_context': if (page::get_current()) {$entity_name = page::get_current()->args_get('entity_name'); if ($entity_name) {$entity = entity::get($entity_name, false); if ($entity) return (new text($entity->title_plural))->render();}} break;
    };
  }

}}