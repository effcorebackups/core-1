<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_number;
          use \effcore\layout;
          use \effcore\markup;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\widget_fields_for_area_part;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      if ($entity->name == 'page' && !empty($form->_instance)) {
      # disable field 'url' for embedded instance
        if (!empty($form->_instance->is_embed)) {
          $items['#url']->disabled_set(true);
        }
      # field 'min width' + field 'max width'
        $width_min = new field_number('Minimum width', new text_multiline(['Value in pixels.', 'Leave 0 if you want to use global page size settings.']), [], -15);
        $width_max = new field_number('Maximum width', new text_multiline(['Value in pixels.', 'Leave 0 if you want to use global page size settings.']), [], -15);
        $width_min->build();
        $width_max->build();
        $width_min->name_set('width_min');
        $width_max->name_set('width_max');
        $width_min->value_set($form->_instance->data['width_min'] ?? 0);
        $width_max->value_set($form->_instance->data['width_max'] ?? 0);
        $width_min->min_set(0    );
        $width_max->min_set(0    );
        $width_min->max_set(10000);
        $width_max->max_set(10000);
        $form->child_select('fields')->child_insert($width_min, 'width_min');
        $form->child_select('fields')->child_insert($width_max, 'width_max');
      # layout and its parts
        $layout = core::deep_clone(layout::select($form->_instance->id_layout));
        if ($layout) {
          foreach ($layout->children_select_recursive() as $c_area) {
            if ($c_area instanceof area) {
                $c_area->managing_enable();
                $c_area->build();
                if ($c_area->id) {
                  $c_widget_parts = new widget_fields_for_area_part($c_area->id);
                  $c_widget_parts->name_prefix = 'parts__'.$c_area->id;
                  $c_widget_parts->cform = $form;
                  $c_widget_parts->build();
                  $c_widget_parts->items_set($form->_instance->parts[$c_area->id] ?? null, true);
                  $c_area->child_insert($c_widget_parts, 'widget_parts');
                  $form->_widgets_area[$c_area->id] = $c_widget_parts;
                }
            }
          }
          $form->child_select('fields')->child_insert(
            new markup('x-layout-manager', ['data-layout-id' => $layout->id], ['manager' => $layout], -20), 'layout_manager'
          );
        } else {
          $form->child_select('fields')->child_insert(
            new markup('x-layout-message', [], ['message' => new text(
              'LOST LAYOUT: %%_id', ['id' => $form->_instance->id_layout ?: 'n/a'])
            ], -20), 'layout_message'
          );
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if ($entity->name == 'page' && !empty($form->_instance)) {
          # field 'min width' + field 'max width'
            $data = $form->_instance->data;
            $data['width_min'] = $items['#width_min']->value_get();
            $data['width_max'] = $items['#width_max']->value_get();
            $form->_instance->data = $data;
          # save layout parts
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