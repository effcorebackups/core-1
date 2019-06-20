<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\button;
          use \effcore\core;
          use \effcore\group_page_part_insert;
          use \effcore\layout;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_init($form, &$items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    if ($entity_name == 'page' && !empty($form->_instance)) {
      $form->validation_data_is_persistent = true;
      $layout = core::deep_clone(layout::select($form->_instance->id_layout));
      foreach ($layout->children_select_recursive() as $c_child) {
        if ($c_child instanceof area && $c_child->id) {
          $c_child->managing_is_on = true;
          $c_child->tag_name = 'div';
          $c_child->build();
          $c_part_insert = new group_page_part_insert();
          $c_part_insert->id_area = $c_child->id;
          $c_part_insert->build();
          $c_child->child_insert($c_part_insert, 'part_insert');
          $form->_parts_insert[$c_child->id] = $c_part_insert;
        }
      }
      $form->child_insert_after(
        new markup('x-layout-manager', [], $layout), 'fields', 'layout_manager'
      );
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'update':
        break;
      default:
        foreach ($form->_parts_insert as $c_part_insert) {
          $id_part = group_page_part_insert::submit($c_part_insert, null, null);
          if ($id_part) {
            $page_parts = $form->validation_cache_get('page_parts');
            $page_parts[$c_part_insert->id_area][$id_part] = $id_part;
            $form->validation_cache_set('page_parts', $page_parts);
            message::insert('ID area = '.$c_part_insert->id_area.'; ID part = '.$id_part);
            return;
          }
        }
    }
  }

}}