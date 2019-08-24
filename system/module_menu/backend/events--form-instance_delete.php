<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\tree_item;
          use \effcore\url;
          abstract class events_form_instance_delete {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'tree_item' && !empty($form->_instance)) {
        $tree_item = tree_item::select(
          $form->_instance->id,
          $form->_instance->id_tree);
        $tree_item->url = '';
        $tree_item->build();
        $tree_item_children = $tree_item->children_select_recursive();
        if ($tree_item_children) {
          $children = new markup('ul');
          $question = new markup('p', [], ['question' => new text('The following related items will also be deleted:'), 'children' => $children]);
          foreach ($tree_item_children as $c_child) {
            $children->child_insert(new markup('li', [], $c_child->id));
            $form->_related[] = $c_child->id;}
          $items['info']->child_insert($question, 'question_for_related');
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'tree_item') {
        $id_tree = $form->_instance->id_tree;
        switch ($form->clicked_button->value_get()) {
          case 'delete':
            if (!empty($form->_related)) {
              $in_values = [];
              foreach ($form->_related as $c_id) $in_values['in_value_'.$c_id.'_!v'] = $c_id;
              $result = entity::get('tree_item')->instances_delete(['conditions' => [
                'id_!f'    => 'id',
                'in_begin' => 'in (',
                'in_!,'    => $in_values,
                'in_end'   => ')']]);
              if ($result) message::insert(new text('Related items of type "%%_name" with id = "%%_id" was deleted.',     ['name' => translation::get($entity->title), 'id' => implode(', ', $form->_related)])         );
              else         message::insert(new text('Related items of type "%%_name" with id = "%%_id" was not deleted!', ['name' => translation::get($entity->title), 'id' => implode(', ', $form->_related)]), 'error');}
                         page::get_current()->args_set('back_delete_0', '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name.'///'.$id_tree); break;
          case 'return': page::get_current()->args_set('back_return_0', '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name.'///'.$id_tree); break;
        }
      }
    }
  }

}}