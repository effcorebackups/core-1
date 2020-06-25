<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\access;
          use \effcore\actions_list;
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\selection;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_instance_select_multiple {

  static function on_init($event, $form, $items) {
    page::get_current()->args_set('action_name', 'select_multiple');
    if (!$form->managing_group_id) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!$form->entity_name      ) $form->entity_name       = page::get_current()->args_get('entity_name');
    $entity = entity::get($form->entity_name);
    if ($entity) {
      $form->_has_access_select = access::check($entity->access_select);
      $form->_has_access_insert = access::check($entity->access_insert);
      $form->_has_access_update = access::check($entity->access_update);
      $form->_has_access_delete = access::check($entity->access_delete);
      $form->attribute_insert('data-entity_name', $form->entity_name);
      $selection = new selection;
      $selection->id = 'instance_select_multiple-'.$entity->name;
      $selection->pager_is_enabled = true;
      foreach ($entity->selection_params_for_managing_multiple as $c_key => $c_value)
        $selection->                                             {$c_key} = $c_value;
      if (empty($selection->decorator_params['view_type']))
                $selection->decorator_params['view_type'] = 'table-adaptive';
      $form->_selection = $selection;
      $has_visible_fields = false;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_on_select_multiple_is_enabled)) {
          $has_visible_fields = true;
          $selection->field_insert_entity(null,
            $entity->name, $c_name, $c_field->selection_params_default ?? []
          );
        }
      }
      if (!$has_visible_fields) {
        $form->child_select('data')->child_insert(
          new markup('x-no-items', ['data-style' => 'table'], 'no fields'), 'no_items'
        );
      } else {
        $selection->field_insert_checkbox(null, null, ['weight' => 500]);
        $selection->field_insert_code('actions', null, function ($c_row, $c_instance) use ($form) {
          $c_actions_list = new actions_list;
          if ($form->_has_access_delete && empty($c_instance->is_embed)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.url::back_part_make(), 'delete');
          if ($form->_has_access_select                                ) $c_actions_list->action_insert($c_instance->make_url_for_select().'?'.url::back_part_make(), 'select');
          if ($form->_has_access_update                                ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.url::back_part_make(), 'update');
          return $c_actions_list;
        }, ['weight' => -500]);
        $selection->build();
        $form->child_select('data')->child_insert($selection, 'selection');
      }
    # disable controls if no access rights
      if (!$form->_has_access_insert) {
        $items['~insert']->disabled_set();
      }
      if (!$form->_has_access_delete) {
        $items['#actions']->is_builded = false;
        $items['#actions']->disabled = ['delete' => 'delete'];
        $items['#actions']->build();
      }
    # disable controls if no items
      if (!count($selection->_instances)) {
        $items['~apply'  ]->disabled_set();
        $items['#actions']->disabled_set();
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'apply':
          if (!$items['#actions']->disabled_get()) {
            $form->_selected_instances = [];
            foreach ($form->_selection->_instances as $c_instance) {
              $c_instance_id = implode('+', $c_instance->values_id_get());
              if (isset($items['#is_checked:'.$c_instance_id]) &&
                        $items['#is_checked:'.$c_instance_id]->checked_get()) {
                   $form->_selected_instances[$c_instance_id] = $c_instance;
              }
            }
            if ($form->_selected_instances === []) {
              message::insert('No one item was selected!', 'warning');
              foreach ($form->_selection->_instances as $c_instance) {
                $c_instance_id = implode('+', $c_instance->values_id_get());
                if (isset($items['#is_checked:'.$c_instance_id]))
                          $items['#is_checked:'.$c_instance_id]->error_set();
              }
            }
          }
          break;
      }
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'apply':
          if (!empty($form->_selected_instances)) {
            foreach ($form->_selected_instances as $c_instance_id => $c_instance) {
              if ($items['#actions']->value_get() === 'delete') {
                if (empty($c_instance->is_embed)) {
                  $c_result = $c_instance->delete();
                  if ($form->is_show_result_message && $c_result !== null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was deleted.',     ['type' => translation::apply($entity->title), 'id' => $c_instance_id])         );
                  if ($form->is_show_result_message && $c_result === null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not deleted!', ['type' => translation::apply($entity->title), 'id' => $c_instance_id]), 'error');
                }
              }
            }
          }
          static::on_init(null, $form, $items);
          break;
        case 'insert':
          url::go($entity->make_url_for_insert().'?'.url::back_part_make());
          break;
      }
    }
  }

}}