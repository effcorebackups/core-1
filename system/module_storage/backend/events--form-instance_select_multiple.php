<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
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
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      $selection = new selection;
      $selection->id = 'instances_manage-'.$entity->name;
      $selection->pager_is_enabled = true;
      foreach ($entity->managing_selection_params as $c_key => $c_value)
        $selection->                                {$c_key} = $c_value;
        $selection->decorator_params['view_type'] = $entity->decorator_view_type_multiple;
      $form->_selection = $selection;
      $has_visible_fields = false;
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_is_on_select)) {
          $has_visible_fields = true;
          $selection->field_insert_entity(null,
            $entity->name, $c_name, $c_field->managing_selection_params ?? []
          );
        }
      }
      if (!$has_visible_fields) {
        $form->child_select('data')->child_insert(
          new markup('x-no-result', [], 'no visible fields'), 'no_result'
        );
      } else {
        $selection->field_insert_checkbox(null, '', ['weight' => 80]);
      # $c_row 'actions'
        $form->_selection->field_insert_code('actions', '', function ($c_row, $c_instance) {
          $c_actions_list = new actions_list();
          if (true && empty($c_instance->is_embed)) $c_actions_list->action_insert('/manage/data/'.$c_instance->entity_get()->group_managing_get_id().'/'.$c_instance->entity_get()->name.'/'.join('+', $c_instance->values_id_get()).'/delete?'.url::back_part_make(), 'delete');
          if (true                                ) $c_actions_list->action_insert('/manage/data/'.$c_instance->entity_get()->group_managing_get_id().'/'.$c_instance->entity_get()->name.'/'.join('+', $c_instance->values_id_get()).       '?'.url::back_part_make(), 'select');
          if (true                                ) $c_actions_list->action_insert('/manage/data/'.$c_instance->entity_get()->group_managing_get_id().'/'.$c_instance->entity_get()->name.'/'.join('+', $c_instance->values_id_get()).'/update?'.url::back_part_make(), 'update');
          return $c_actions_list;
        });
        $selection->build();
        $form->child_select('data')->child_insert($selection, 'selection');
        if (!count($selection->_instances)) {
          $items['~apply'  ]->disabled_set();
          $items['#actions']->disabled_set();
        }
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
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
            if ($form->_selected_instances == []) {
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
    $managing_group_id = page::get_current()->args_get('managing_group_id');
    $entity_name       = page::get_current()->args_get('entity_name'      );
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'apply':
          if (!empty($form->_selected_instances)) {
            foreach ($form->_selected_instances as $c_instance_id => $c_instance) {
              if ($items['#actions']->value_get() == 'delete') {
                if (empty($c_instance->is_embed) && $c_instance->delete())
                     message::insert(new text('Item of type "%%_name" with id = "%%_id" was deleted.',     ['name' => translation::get($entity->title), 'id' => $c_instance_id])         );
                else message::insert(new text('Item of type "%%_name" with id = "%%_id" was not deleted!', ['name' => translation::get($entity->title), 'id' => $c_instance_id]), 'error');
              }
            }
          }
          static::on_init(null, $form, $items);
          break;
        case 'insert':
          url::go('/manage/data/'.$managing_group_id.'/'.$entity->name.'//insert'.'?'.url::back_part_make());
          break;
      }
    }
  }

}}