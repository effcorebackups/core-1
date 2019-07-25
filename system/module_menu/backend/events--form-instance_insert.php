<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\tree;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $category    = page::get_current()->args_get('category'   );
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'id_tree'
      if ($entity->name == 'tree_item') {
        $items['#id_tree']->value_set(
          tree::select($category) ? $category : null
        );
      }
    }
  }

}}