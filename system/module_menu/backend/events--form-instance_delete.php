<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
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
      foreach ($tree_item->children_select_recursive() as $c_child) {
        $form->_related[$c_child->id] = $c_child;
        $c_child->url = '';
      }
      if (isset($form->_related))
           $question = new markup('p', [], 'Delete all of these items?');
      else $question = new markup('p', [], 'Delete item?');
      $items['info']->child_insert($question,                        'question');
      $items['info']->child_insert(new markup('ul', [], $tree_item), 'sub_tree');
    }
  }

}}