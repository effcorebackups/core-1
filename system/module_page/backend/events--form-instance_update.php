<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
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
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
        if (!empty($form->_instance->is_embed)) {
          $items['#url']->disabled_set(true);
        }
      # init cache pool
        $cache = $form->validation_cache_get('page_parts');
        if ($cache === null) {
          $cache = [];
          foreach ($form->_instance->parts ?: [] as $c_id_area => $c_old_parts)
            foreach ($c_old_parts as $c_id_part => $c_part)
                  $cache[$c_id_area][$c_id_part] = $c_part;
          $form->validation_cache_set('page_parts', $cache);
        }
      # build layout
        $form->_parts_manage = [];
        $form->_parts_insert = [];
        $layout = core::deep_clone(layout::select($form->_instance->id_layout));
        foreach ($layout->children_select_recursive() as $c_area) {
          if ($c_area instanceof area && $c_area->id) {
            $c_area->managing_is_on = true;
            $c_area->tag_name = 'div';
            $c_area->build();
            foreach ($cache[$c_area->id] ?? [] as $c_part)
              if ($c_part instanceof page_part_preset_link) {
                $c_preset = $c_part->page_part_preset_get();
                $c_part_manage = new group_page_part_manage;
                $c_part_manage->id_area   = $c_area  ->id;
                $c_part_manage->id_preset = $c_preset->id;
                $c_part_manage->build();
                $c_area->child_insert($c_part_manage, 'part_manage_'.$c_preset->id);
                $form->_parts_manage[$c_area->id.'-'.$c_preset->id] = $c_part_manage;}
            $c_part_insert = new group_page_part_insert;
            $c_part_insert->id_area = $c_area->id;
            $c_part_insert->build();
            $c_area->child_insert($c_part_insert, 'part_insert');
            $form->_parts_insert[$c_area->id] = $c_part_insert;
          }
        }
        $form->child_delete('layout_manager'    );
        $form->child_delete('button_update_copy');
        $form->child_delete('button_cancel_copy');
        $form->child_insert(new markup('x-layout-manager', [], $layout), 'layout_manager');
        $form->child_insert(core::deep_clone($items['~update']), 'button_update_copy');
        $form->child_insert(core::deep_clone($items['~cancel']), 'button_cancel_copy');
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
        $cache = $form->validation_cache_get('page_parts');
        switch ($form->clicked_button->value_get()) {
          case 'cancel':
            break;
          case 'update':
            $new_parts = [];
            foreach ($cache as $c_id_area => $c_new_parts)
              foreach ($c_new_parts as $c_id_part => $c_part)
                $new_parts[$c_id_area][$c_id_part] = $c_part;
            $form->_instance->parts = $new_parts ?: null;
            break;
          default:
            $manage_result = null;
            $insert_result = null;
            foreach ($form->_parts_manage as $c_part_manage) {$manage_result = group_page_part_manage::submit($c_part_manage, null, null); if ($manage_result) break;}
            foreach ($form->_parts_insert as $c_part_insert) {$insert_result = group_page_part_insert::submit($c_part_insert, null, null); if ($insert_result) break;}
            if ($manage_result) {
              unset($cache[$manage_result->id_area][$manage_result->id_preset]);
              $form->validation_data_is_persistent = true;
              $form->validation_cache_set('page_parts', $cache);
              message::insert(new text('Part of the page with id = "%%_id_page_part" was deleted from the area with id = "%%_id_area".', ['id_page_part' => $manage_result->id_preset, 'id_area' => $manage_result->id_area]));
              static::on_init(null, $form, $items);
              return;
            } else if ($insert_result) {
              $cache[$insert_result->id_area][$insert_result->id_preset] = new page_part_preset_link($insert_result->id_preset);
              $form->validation_data_is_persistent = true;
              $form->validation_cache_set('page_parts', $cache);
              message::insert(new text('Part of the page with id = "%%_id_page_part" was inserted to the area with id = "%%_id_area".', ['id_page_part' => $insert_result->id_preset, 'id_area' => $insert_result->id_area]));
              static::on_init(null, $form, $items);
              return;
            }
        }
      }
    }
  }

}}