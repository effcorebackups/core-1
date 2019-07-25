<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\tree_item;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'parent'
      if ($entity->name == 'tree_item' && !empty($form->_instance)) {
        $tree_item = tree_item::select($form->_instance->id, $form->_instance->id_tree);
        $tree_item->build();
        foreach ($tree_item->children_select_recursive() as $c_child)
          $items['#id_parent']->disabled[$c_child        ->id] = $c_child        ->id;
          $items['#id_parent']->disabled[$form->_instance->id] = $form->_instance->id;
          $items['#id_parent']->is_builded = false;
          $items['#id_parent']->query_params['conditions'] = ['id_tree_!f' => 'id_tree', 'operator' => '=', 'id_tree_!v' => $form->_instance->id_tree];
          $items['#id_parent']->build();
          $items['#id_parent']->value_set(
            $form->_instance->id_parent
          );
      }
    }
  }

}}