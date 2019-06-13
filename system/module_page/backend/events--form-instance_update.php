<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\area;
          use \effcore\core;
          use \effcore\field_page_part;
          use \effcore\layout;
          use \effcore\markup;
          use \effcore\page;
          abstract class events_form_instance_update {

  static function on_init($form, &$items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    if ($entity_name == 'page' && !empty($form->_instance)) {
      $layout = core::deep_clone(layout::select($form->_instance->id_layout));
      foreach ($layout->children_select_recursive() as $c_child) {
        if ($c_child instanceof area) {
          $c_child->managing_is_on = true;
          $c_child->tag_name = 'div';
          $c_field_page_part = new field_page_part;
          $c_field_page_part->id_area = $c_child->id;
          $c_field_page_part->build();
          $c_child->child_insert($c_field_page_part);
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
    }
  }

}}