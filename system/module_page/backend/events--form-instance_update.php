<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\layout;
          use \effcore\markup;
          use \effcore\page;
          use \effcore\text;
          use \effcore\widget_parts;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
      # disable url field for embedded instance
        if (!empty($form->_instance->is_embed)) {
          $items['#url']->disabled_set(true);
        }
      # build layout
        $layout = core::deep_clone(layout::select($form->_instance->id_layout));
        if ($layout) {
          foreach ($layout->children_select_recursive() as $c_area) {
            if ($c_area instanceof area &&
                $c_area->id) {
                $c_area->managing_enable();
                $c_area->build();
                $c_widget_parts = new widget_parts('parts__'.$c_area->id, $c_area->id);
                $c_widget_parts->form_current_set($form);
                $c_widget_parts->build();
                $c_widget_parts->items_set_once($form->_instance->parts[$c_area->id] ?? null);
                $c_area->child_insert($c_widget_parts, 'widget_parts');
                $form->_widgets_area[$c_area->id] = $c_widget_parts;
            }
          }
          $form->child_select('fields')->child_insert(
            new markup('x-layout-manager', ['data-layout-id' => $layout->id], ['manager' => $layout], -20), 'layout_manager'
          );
        } else {
          $form->child_select('fields')->child_insert(
            new markup('x-layout-message', [], ['message' => new text(
              'LOST LAYOUT: %%_id', ['id' => $form->_instance->id_layout])
            ], -20), 'layout_message'
          );
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'page' && !empty($form->_instance)) {
            if (layout::select($form->_instance->id_layout)) {
              $all_parts = [];
              foreach ($form->_widgets_area as $c_id_area => $c_widget) {
                $c_parts = $c_widget->items_get();
                if ($c_parts)
                  $all_parts[$c_id_area] = $c_parts;
              }
              if (count($all_parts))
                   $form->_instance->parts = $all_parts;
              else $form->_instance->parts = null;
            }
          }
          break;
      }
    }
  }

}}