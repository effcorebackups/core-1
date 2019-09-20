<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\group_page_part_insert;
          use \effcore\group_page_part_manage;
          use \effcore\layout;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page_part_preset_link;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_instance_update_page {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
      # disable url field for embedded instance
        if (!empty($form->_instance->is_embed)) {
          $items['#url']->disabled_set(true);
        }
      # init pool of parts
        if      ($form->validation_cache_get('parts') === null)
                 $form->validation_cache_set('parts', $form->_instance->parts ?: []);
        $parts = $form->validation_cache_get('parts');
      # build layout
        $layout = core::deep_clone(layout::select($form->_instance->id_layout));
        foreach ($layout->children_select_recursive() as $c_area) {
          if ($c_area instanceof area && $c_area->id) {
            $c_area->managing_is_on = true;
            $c_area->tag_name = 'div';
            $c_area->build();
          # insert groups 'Field manage'
            foreach ($parts[$c_area->id] ?? [] as $c_part) {
              if ($c_part instanceof page_part_preset_link) {
                $c_part_manage = new group_page_part_manage;
                $c_part_manage->id_area   = $c_area->id;
                $c_part_manage->id_preset = $c_part->id;
                $c_part_manage->build();
                $c_area->child_insert($c_part_manage, 'part_manage_'.$c_part->id);
              }
            }
          # insert group 'Part insert'
            $c_part_insert = new group_page_part_insert;
            $c_part_insert->id_area = $c_area->id;
            $c_part_insert->build();
            $c_area->child_insert($c_part_insert, 'part_insert');
          }
        }
        $form->child_select('fields')->child_delete(                                             'layout_manager');
        $form->child_select('fields')->child_insert(new markup('x-layout-manager', [], $layout), 'layout_manager');
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
        if ($form->clicked_button->value_get() == 'update')
          $form->_instance->parts = $form->validation_cache_get('parts') ?: null;
        else {
        # manual submit for groups (widgets)
          foreach ($items as $c_npath => $c_item)
            if (is_object($c_item) && method_exists($c_item, 'submit'))
              $c_item::submit($c_item, $form, $c_npath);
          static::on_init(null, $form, $items);
        }
      }
    }
  }

}}