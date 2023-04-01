<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\text;
          use \effcore\tree_item;
          use \effcore\url;
          abstract class events_form_instance_delete {

  static function on_build($event, $form) {
    if ($form->has_error_on_build === false) {
      $entity = entity::get($form->entity_name);
      if ($entity->name === 'tree_item') {
        $tree_item = tree_item::select($form->_instance->id, $form->_instance->id_tree);
        $tree_item->url = '';
        $tree_item->build();
        $tree_item_children = $tree_item->children_select_recursive();
        if ($tree_item_children) {
          $children = new markup('ul');
          $question = new markup('p', [], ['question' => new text('The following related items will also be deleted:'), 'children' => $children]);
          foreach ($tree_item_children as $c_child) {
            $children->child_insert(new markup('li', [], $c_child->id));
            $form->_related[] = $c_child->id; }
          $form->child_select('info')->child_insert($question, 'question_for_related');
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'delete':
      case 'cancel':
        if ($entity->name === 'tree_item') {
          if (!url::back_url_get())
               url::back_url_set('back', $entity->make_url_for_select_multiple().'///'.$form->_instance->id_tree);
        }
        break;
    }
  }

}}