<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\tree_item;
          abstract class events_form_instance_delete {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get('tree_item');
    if ($entity) {
      if ($entity->name == 'tree_item' && $form->_instance) {
        $tree_item = tree_item::select(
          $form->_instance->id,
          $form->_instance->id_tree);
        $tree_item->url = '';
        $tree_item->build();
        $tree_item_children = $tree_item->children_select_recursive();
        if ($tree_item_children) {
          foreach ($tree_item_children as $c_child) {$form->_related[] = $c_child->id; $c_child->url = '';}
          $question = new markup('p', [], new text('Delete related items of type "%%_name" with id = "%%_id"?', ['name' => translation::get($entity->title), 'id' => implode(', ', $form->_related)]));
          $items['info']->child_insert($question, 'question_for_related');
        }
      }
    }
  }

  static function on_submit($form, $items) {
    $entity = entity::get('tree_item');
    switch ($form->clicked_button->value_get()) {
      case 'delete':
        if (isset($form->_related)) {
          $in_values = [];
          foreach ($form->_related as $c_id) $in_values['in_value_'.$c_id.'_!v'] = $c_id;
          $result = entity::get('tree_item')->instances_delete(['conditions' => [
            'id_!f'    => 'id',
            'in_begin' => 'in (',
            'in_!,'    => $in_values,
            'in_end'   => ')'
          ]]);
          if ($result) message::insert(new text('Related items of type "%%_name" with id = "%%_id" was deleted.',     ['name' => translation::get($entity->title), 'id' => implode(', ', $form->_related)])         );
          else         message::insert(new text('Related items of type "%%_name" with id = "%%_id" was not deleted!', ['name' => translation::get($entity->title), 'id' => implode(', ', $form->_related)]), 'error');
        }
        break;
    }
  }

}}