<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\tree_item;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_init === false) {
      $entity = entity::get($form->entity_name);
      if ($entity) {
        if ($entity->name === 'tree_item' && !empty($form->_instance)) {
        # field 'parent'
          $tree_item = tree_item::select(
            $form->_instance->id,
            $form->_instance->id_tree);
          $tree_item->build();
          foreach ($tree_item->children_select_recursive() as $c_child) $items['#id_parent']->disabled[$c_child->id] = $c_child->id;
          $items['#id_parent']->disabled[$form->_instance->id] = $form->_instance->id;
          $items['#id_parent']->is_builded = false;
          $items['#id_parent']->query_settings['conditions'] = [
            'id_tree_!f'       => 'id_tree',
            'id_tree_operator' => '=',
            'id_tree_!v'       => $form->_instance->id_tree];
          $items['#id_parent']->build();
          $items['#id_parent']->value_set(
            $form->_instance->id_parent
          );
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
        case 'cancel':
          if ($entity->name === 'tree_item' && !empty($form->_instance)) {
            if (!url::back_url_get())
                 url::back_url_set('back', $entity->make_url_for_select_multiple().'///'.$form->_instance->id_tree);
          }
          break;
      }
    }
  }

}}