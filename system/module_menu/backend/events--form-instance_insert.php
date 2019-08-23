<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\entity;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\translation;
          use \effcore\tree_item;
          use \effcore\tree;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $category_id = page::get_current()->args_get('category_id');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'id_tree'
      if ($entity->name == 'tree_item') {
        $items['#id_tree']->value_set(
          tree::select($category_id) ? $category_id : null
        );
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
    # field 'id'
      if ($entity->name == 'tree') {
        if ($items['#id']->value_get()) {
          if (tree::select($items['#id']->value_get())) {
            $items['#id']->error_set(new text_multiline([
              'Field "%%_title" contains the previously used value!',
              'Only unique value is allowed.'], ['title' => translation::get($items['#id']->title)]
            ));
          }
        }
      }
    # field 'id'
      if ($entity->name == 'tree_item') {
        if ($items['#id']->value_get()) {
          if (tree_item::select($items['#id']->value_get(), null)) {
            $items['#id']->error_set(new text_multiline([
              'Field "%%_title" contains the previously used value!',
              'Only unique value is allowed.'], ['title' => translation::get($items['#id']->title)]
            ));
          }
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'tree_item') {
        $id_tree = page::get_current()->args_get('category_id');
        switch ($form->clicked_button->value_get()) {
          case 'insert': page::get_current()->args_set('back_insert_0', '/manage/data/select_multiple/'.$entity->group_managing_get_id().'/'.$entity->name.'/'.$id_tree);
          case 'return': page::get_current()->args_set('back_return_0', '/manage/data/select_multiple/'.$entity->group_managing_get_id().'/'.$entity->name.'/'.$id_tree);
        }
      }
    }
  }

}}