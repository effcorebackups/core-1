<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\page;
          use \effcore\tree_item;
          abstract class events_form_instance_delete {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    if ($entity_name == 'tree_item' && $form->_instance) {
      $tree_item = tree_item::select($instance_id, $form->_instance->id_tree);
      $tree_item->url = '';
      $tree_item->build();
      $form->_ids = [$tree_item->id];
      foreach ($tree_item->children_select_recursive() as $c_child) {
        $form->_ids[] = $c_child->id;
        $c_child->url = '';
      }
      if (count($form->_ids) > 1)
           $question = new markup('p', [], 'Delete all items below?');
      else $question = new markup('p', [], 'Delete item?');
      $items['info']->child_insert($question,                        'question');
      $items['info']->child_insert(new markup('ul', [], $tree_item), 'sub_tree');
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'delete':
        if (isset($form->_ids)) {
          $in_values = [];
          foreach ($form->_ids as $c_id) $in_values['in_value_'.$c_id.'_!v'] = $c_id;
          entity::get('tree_item')->instances_delete(['conditions' => [
            'id_!f'    => 'id',
            'in_begin' => 'in (',
            'in_!,'    => $in_values,
            'in_end'   => ')'
          ]]);
        }
        break;
    }
  }

}}