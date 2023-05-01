<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\actions_list;
          use \effcore\entity;
          use \effcore\field_hidden;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\node;
          use \effcore\page;
          use \effcore\request;
          use \effcore\text;
          use \effcore\tree;
          use \effcore\url;
          abstract class events_form_instance_select_multiple {

  static function on_build($event, $form) {
    if ($form->has_error_on_build === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'tree_item') {
        if (!$form->category_id)
             $form->category_id = page::get_current()->args_get('category_id');
        $trees = tree::select_all('sql');
        if (isset($trees[$form->category_id])) {
        # drag-and-drop functionality
          $form->_selection->is_builded = false;
          $form->_selection->query_settings['conditions'] = [
            'id_tree_!f'       => 'id_tree',
            'id_tree_operator' => '=',
            'id_tree_!v'       => $form->category_id];
        # field 'extra'
          $form->_selection->fields['code']['extra'] = new \stdClass;
          $form->_selection->fields['code']['extra']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) {
            return new node([], [
              'actions'       => $c_row['actions']['value'],
              'hidden_parent' => new field_hidden('parent-'.$c_instance->id, $c_instance->id_parent, ['data-type' => 'parent']),
              'hidden_weight' => new field_hidden('weight-'.$c_instance->id, $c_instance->weight,    ['data-type' => 'weight'])
            ]);
          };
          $form->_selection->build();
        } else {
          $form->child_select('data')->child_delete('selection');
          $form->child_select('data')->child_insert(new markup('x-no-items', ['data-style' => 'table'], 'wrong category'), 'message_error');
          $form->has_error_on_build = true;
        }
      }
    }
  }

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_build === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'tree_item') {
        $items['#actions']->disabled_set();
        $items[ '~apply' ]->disabled_set(count($form->_selection->_instances) === 0);
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
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
          static::on_build(null, $form);
          static::on_init (null, $form, $items);
        }
        break;
      case 'insert':
        if ($entity->name === 'tree_item' && $form->category_id) {
          url::go($entity->make_url_for_insert().'/'.$form->category_id.'?'.url::back_part_make());
        }
        break;
    }
  }

}}