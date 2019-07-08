<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\selection;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_instance_select_multiple {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      $items['~add_new']->attribute_insert('title', new text('Add new instance of type %%_name on new page.', ['name' => translation::get($entity->title)]));
      $selection = new selection('', $entity->view_type_multiple);
      $selection->id = 'instances_manage';
      $selection->is_paged = true;
      $form->_selection = $selection;
      foreach ($entity->selection_params as $c_key => $c_value) {
        $selection->                       {$c_key} = $c_value;
      }
      $has_visible_fields = false;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->field_is_visible_on_select)) {
          $has_visible_fields = true;
          $selection->field_insert_entity(null, $entity->name, $c_name);
        }
      }
      if (!$has_visible_fields) {
        $form->child_select('data')->child_insert(
          new markup('x-no-result', [], 'no visible fields'), 'no_result'
        );
      } else {
        $selection->field_insert_checkbox(null, '', 80);
        $selection->field_insert_action(null, '', ['delete', 'select', 'update']);
        $selection->build();
        $form->child_select('data')->child_insert($selection, 'selection');
        if (!count($selection->_instances)) {
          $items['~apply'  ]->disabled_set();
          $items['#actions']->disabled_set();
        }
      }
    }
  }

  static function on_submit($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        if (!$items['#actions']->disabled_get()) {
          $has_selection = false;
          foreach ($form->_selection->_instances as $c_instance) {
            $id_values = implode('+', $c_instance->values_id_get());
            if ($items['#is_checked:'.$id_values]->checked_get()) {
              $has_selection = true;
              message::insert(new text('Instance with id = "%%_id" was selected.', ['id' => $id_values]));
            }
          }
          if (!$has_selection) {
            message::insert(
              'Nothing selected!', 'warning'
            );
          }
        }
        break;
      case 'add_new':
        url::go('/manage/instance/insert/'.$entity_name.'?'.url::back_part_make());
        break;
    }
  }

}}