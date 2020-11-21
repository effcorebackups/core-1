<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\complex_control;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_checkbox;
          use \effcore\field_hidden;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text_multiline;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $items['~update']->disabled_set();
    if (!$form->managing_group_id) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!$form->entity_name      ) $form->entity_name       = page::get_current()->args_get('entity_name');
    if (!$form->instance_id      ) $form->instance_id       = page::get_current()->args_get('instance_id');
    $entity = entity::get($form->entity_name);
    $groups = entity::get_managing_group_ids();
    if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
      if ($entity) {
        if ($entity->managing_is_enabled) {
          $id_keys   = $entity->id_get_real();
          $id_values = explode('+', $form->instance_id);
          if (count($id_keys) ==
              count($id_values)) {
            $conditions = array_combine($id_keys, $id_values);
            $form->_instance = new instance($form->entity_name, $conditions);
            if ($form->_instance->select()) {
              $form->attribute_insert('data-entity_name', $form->entity_name);
              $form->attribute_insert('data-instance_id', $form->instance_id);
              $items['~update']->disabled_set(false);
            # fixation of 'updated' value for prevent parallel update (not secure: only for organizational methods)
              if ($entity->has_parallel_checking && $entity->field_get('updated')) {
                $hidden_old_updated = new field_hidden('old_updated');
                $hidden_old_updated->value_set(core::sanitize_datetime($hidden_old_updated->value_request_get()) ?: $form->_instance->updated);
                $form->child_insert($hidden_old_updated, 'hidden_old_updated');
              }
            # make controls for managing
              $has_controls = false;
              foreach ($entity->fields as $c_name => $c_field) {
                if (!empty($c_field->managing_on_update_is_enabled) &&
                     isset($c_field->managing_control_class)) {
                  $c_control = new $c_field->managing_control_class;
                  $c_control->title = $c_field->title;
                  $c_control->element_attributes['name'] = $c_name;
                  $c_control->element_attributes = ($c_field->managing_control_element_attributes           ?? []) + $c_control->element_attributes;
                  $c_control->element_attributes = ($c_field->managing_control_element_attributes_on_update ?? []) + $c_control->element_attributes;
                  foreach ($c_field->managing_control_properties           ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                  foreach ($c_field->managing_control_properties_on_update ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                  $c_control->cform = $form;
                  $c_control->entity_name = $entity->name;
                  $c_control->entity_field_name = $c_name;
                  $c_control->build();
                  $c_control->value_set_initial($form->_instance->{$c_name}, true);
                  if     (empty($c_field->managing_control_value_manual_set) && $c_control instanceof complex_control        ) $c_control->value_set_complex($form->_instance->{$c_name}, true);
                  elseif (empty($c_field->managing_control_value_manual_set) && $c_control instanceof field_checkbox !== true) $c_control->value_set        ($form->_instance->{$c_name});
                  elseif (empty($c_field->managing_control_value_manual_set) && $c_control instanceof field_checkbox === true) $c_control->checked_set      ($form->_instance->{$c_name});
                  $items['fields']->child_insert($c_control, $c_name);
                  if ($c_control->disabled_get() === false) {
                    $has_controls = true;
                  }
                }
              }
              if ($items['fields']->children_select_count() === 0 || $has_controls === false) $items['~update']->disabled_set();
              if ($items['fields']->children_select_count() === 0) {
                $form->child_update(
                  'fields', new markup('x-no-items', ['data-style' => 'table'], 'no fields')
                );
              }
            } else $items['fields']->child_insert(new markup('p', [], new text('wrong instance key'                         )), 'error_message');
          }   else $items['fields']->child_insert(new markup('p', [], new text('wrong instance keys'                        )), 'error_message');
        }     else $items['fields']->child_insert(new markup('p', [], new text('management for this entity is not available')), 'error_message');
      }       else $items['fields']->child_insert(new markup('p', [], new text('wrong entity name'                          )), 'error_message');
    }         else $items['fields']->child_insert(new markup('p', [], new text('wrong management group'                     )), 'error_message');
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if (!empty($form->_instance)) {
          # prevent parallel update
            if ($entity->has_parallel_checking && $entity->field_get('updated')) {
              $hidden_old_updated = $form->child_select('hidden_old_updated');
              $hidden_old_updated->value_get();
              $fresh_instance = core::deep_clone($form->_instance);
              $fresh_instance->select();
              if ($fresh_instance->updated != $hidden_old_updated->value_get()) {
                $form->error_set(new text_multiline([
                  'While editing this form, someone made changes in parallel and saved them!',
                  'Reload this page and make changes again to prevent inconsistency.']));
                return;
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
        case 'update':
          if (!empty($form->_instance)) {
          # transfer new values to instance
            foreach ($entity->fields as $c_name => $c_field) {
              if (!empty($c_field->managing_on_update_is_enabled) &&
                   isset($c_field->managing_control_class)) {
                $c_value = null;
                $c_reflection = new \ReflectionClass($c_field->managing_control_class);
                $c_prefix = $c_reflection->implementsInterface('\\effcore\\complex_control') ? '*' : '#';
                if     ($items[$c_prefix.$c_name] instanceof complex_control        ) $c_value = $items[$c_prefix.$c_name]->value_get_complex();
                elseif ($items[$c_prefix.$c_name] instanceof field_checkbox !== true) $c_value = $items[$c_prefix.$c_name]->value_get        ();
                elseif ($items[$c_prefix.$c_name] instanceof field_checkbox === true) $c_value = $items[$c_prefix.$c_name]->checked_get      () ? 1 : 0;
                if (!empty($c_field->managing_control_value_manual_get_if_empty) && $c_value == '') continue;
                if (!empty($c_field->managing_control_value_manual_get         )                  ) continue;
                $form->_instance->{$c_name} = $c_value;
              }
            }
          # update action
            $form->_result = $form->_instance->update();
          # show messages
            if ($form->is_show_result_message && $form->_result !== null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was updated.',     ['type' => (new text($entity->title))->render(), 'id' => implode('+', $form->_instance->values_id_get()) ])           );
            if ($form->is_show_result_message && $form->_result === null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not updated!', ['type' => (new text($entity->title))->render(), 'id' => implode('+', $form->_instance->values_id_get()) ]), 'warning');
          # update 'updated' value
            if ($form->_result && $entity->has_parallel_checking && $entity->field_get('updated')) {
              $form->child_select('hidden_old_updated')->value_set(
                $form->_instance->updated
              );
            }
          }
        # ↓↓↓ no break ↓↓↓
        case 'cancel':
        # going back
          if ($form->is_redirect_enabled) {
            $back_update_0 = page::get_current()->args_get('back_update_0');
            $back_update_n = page::get_current()->args_get('back_update_n');
            url::go($back_update_0 ?: (url::back_url_get() ?: (
                    $back_update_n ?: $entity->make_url_for_select_multiple() )));
          }
          break;
      }
    }
  }

}}