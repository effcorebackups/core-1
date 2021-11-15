<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\actions_list;
          use \effcore\entity;
          use \effcore\field_hidden;
          use \effcore\message;
          use \effcore\node;
          use \effcore\page;
          use \effcore\request;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_select_multiple {

  static function on_init($event, $form, $items) {
    if (!$form->category_id) $form->category_id = page::get_current()->args_get('category_id');
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name === 'tree_item' && $form->category_id && !empty($form->_selection)) {
      # drag-and-drop functionality
        $items['#actions']->disabled_set();
        $form->_selection->is_builded = false;
        $form->_selection->query_settings['conditions'] = [
          'id_tree_!f' => 'id_tree',
          'operator'   => '=',
          'id_tree_!v' => $form->category_id];
      # $c_row 'actions'
        $form->_selection->field_insert_code('actions', null, function ($c_row_id, $c_row, $c_instance, $settings = []) use ($form) {
          $c_actions_list = new actions_list;
          if ($form->_has_access_delete && empty($c_instance->is_embedded)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.url::back_part_make(), 'delete');
          if ($form->_has_access_select                                   ) $c_actions_list->action_insert($c_instance->make_url_for_select().'?'.url::back_part_make(), 'select');
          if ($form->_has_access_update                                   ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.url::back_part_make(), 'update');
          return $c_actions_list;
        });
      # $c_row 'extra'
        $form->_selection->field_insert_code('extra', null, function ($c_row_id, $c_row, $c_instance, $settings = []) {
          $c_hidden_parent = new field_hidden('parent-'.$c_instance->id, $c_instance->id_parent, ['data-type' => 'parent']);
          $c_hidden_weight = new field_hidden('weight-'.$c_instance->id, $c_instance->weight,    ['data-type' => 'weight']);
          return new node([], [
            'actions'       => $c_row['actions']['value'],
            'hidden_parent' => $c_hidden_parent,
            'hidden_weight' => $c_hidden_weight
          ]);
        });
        $form->_selection->build();
        if (!count($form->_selection->_instances)) {
          $items['~apply']->disabled_set();
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'apply':
          if ($entity->name === 'tree_item' && $form->category_id) {
            $event->is_last = true;
            $has_changes = false;
            $tree_items = entity::get('tree_item')->instances_select(['conditions' => [
              'id_tree_!f'       => 'id_tree',
              'id_tree_operator' => '=',
              'id_tree_!v'       => $form->category_id]], 'id');
            foreach ($tree_items as $c_item) {
              $c_new_parent = request::value_get('parent-'.$c_item->id) ?: null;
              $c_new_weight = request::value_get('weight-'.$c_item->id) ?: '0';
              if ( ($c_new_parent === null || isset($tree_items[$c_new_parent])) &&
                   ($c_new_weight ===              (string)(int)$c_new_weight) ) {
                if ($c_item->id_parent !== $c_new_parent ||
                    $c_item->weight    !== $c_new_weight) {
                    $c_item->id_parent  =  $c_new_parent;
                    $c_item->weight     =  $c_new_weight;
                  $has_changes = true;
                  $c_result = $c_item->update();
                  if ($form->is_show_result_message && $c_result !== null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was updated.',     ['type' => (new text($entity->title))->render(), 'id' => $c_item->id])           );
                  if ($form->is_show_result_message && $c_result === null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not updated!', ['type' => (new text($entity->title))->render(), 'id' => $c_item->id]), 'warning');
                }
              }
            }
            if ($form->is_show_result_message && !$has_changes) {
              message::insert(
                'You have not made any changes before!', 'warning'
              );
            }
            static::on_init(null, $form, $items);
          }
          break;
        case 'insert':
          if ($entity->name === 'tree_item' && $form->category_id) {
            url::go($entity->make_url_for_insert().'/'.$form->category_id.'?'.url::back_part_make());
          }
          break;
      }
    }
  }

}}