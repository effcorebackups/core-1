<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\control_complex;
          use \effcore\control;
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

  static function on_build($event, $form) {
    if (!$form->managing_group_id) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!$form->entity_name      ) $form->entity_name       = page::get_current()->args_get('entity_name');
    if (!$form->instance_id      ) $form->instance_id       = page::get_current()->args_get('instance_id');
    $entity = entity::get($form->entity_name);
    $groups = entity::get_managing_group_ids();
    if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
      if ($entity) {
        if ($entity->managing_is_enabled) {
          $id_keys   = $entity->id_get();
          $id_values = explode('+', $form->instance_id);
          if (count($id_keys) ===
              count($id_values)) {
            $conditions = array_combine($id_keys, $id_values);
            $form->_instance = new instance($form->entity_name, $conditions);
            if ($form->_instance->select()) {
              $form->attribute_insert('data-entity_name', $form->entity_name);
              $form->attribute_insert('data-instance_id', $form->instance_id);
              $form->child_select('fields')->children_delete();
              foreach ($entity->fields as $c_name => $c_field) {
                if (!empty($c_field->managing_is_enabled_on_update) &&
                     isset($c_field->managing_control_class)) {
                  $c_control = new $c_field->managing_control_class;
                  $c_control->title = $c_field->title;
                  $c_control->element_attributes['name'] = $c_name;
                  $c_control->element_attributes = ($c_field->managing_control_element_attributes           ?? []) + $c_control->element_attributes;
                  $c_control->element_attributes = ($c_field->managing_control_element_attributes_on_update ?? []) + $c_control->element_attributes;
                  if (isset($c_field->managing_control_properties          ) && is_array($c_field->managing_control_properties          )) foreach ($c_field->managing_control_properties           as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                  if (isset($c_field->managing_control_properties_on_update) && is_array($c_field->managing_control_properties_on_update)) foreach ($c_field->managing_control_properties_on_update as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                  $c_control->cform = $form;
                  $c_control->entity_name = $entity->name;
                  $c_control->entity_field_name = $c_name;
                  $form->child_select('fields')->child_insert($c_control, $c_name);
                }
              }
            # form messages
              if ($form->child_select('fields')->children_select_count() === 0) {
                  $form->child_select('fields')->child_insert(new markup('x-no-items', [], 'No fields.'), 'message_no_fields');
                  $form->has_no_fields = true;
              }
            # prevent parallel update (organizational methods, not secure)
              if ($form->has_no_fields === false && $entity->has_parallel_checking && $entity->field_get('updated')) {
                $form->child_insert(new field_hidden('old_updated'), 'hidden_old_updated');
              }
            } else {$form->child_select('fields')->child_insert(new markup('p', [], new text('wrong instance key'                         )), 'message_error'); $form->has_error_on_build = true;}
          }   else {$form->child_select('fields')->child_insert(new markup('p', [], new text('wrong number of instance keys'              )), 'message_error'); $form->has_error_on_build = true;}
        }     else {$form->child_select('fields')->child_insert(new markup('p', [], new text('management for this entity is not available')), 'message_error'); $form->has_error_on_build = true;}
      }       else {$form->child_select('fields')->child_insert(new markup('p', [], new text('wrong entity name'                          )), 'message_error'); $form->has_error_on_build = true;}
    }         else {$form->child_select('fields')->child_insert(new markup('p', [], new text('wrong management group'                     )), 'message_error'); $form->has_error_on_build = true;}
  }

  static function on_init($event, $form, $items) {
    if ($form->has_error_on_build === false &&
        $form->has_no_fields      === false) {
      $entity = entity::get($form->entity_name);
      if (isset($items['~update'])) $items['~update']->disabled_set(false);
      if (isset($items['~cancel'])) $items['~cancel']->disabled_set(false);
      foreach ($entity->fields as $c_name => $c_field) {
        if (!empty($c_field->managing_is_enabled_on_update) &&
             isset($c_field->managing_control_class)) {
          $c_reflection = new \ReflectionClass($c_field->managing_control_class);
          $c_prefix = $c_reflection->implementsInterface('\\effcore\\control_complex') ? '*' : '#';
          $c_control = $items[$c_prefix.$c_name];
          $c_control->value_set_initial($form->_instance->{$c_name}, true);
          if     (empty($c_field->managing_control_value_manual_set) && $c_control instanceof control && $c_control instanceof field_checkbox !== true) $c_control->  value_set($form->_instance->{$c_name}, ['once' => true]);
          elseif (empty($c_field->managing_control_value_manual_set) && $c_control instanceof control && $c_control instanceof field_checkbox === true) $c_control->checked_set($form->_instance->{$c_name});
        }
      }
    # prevent parallel update
      if ($form->has_no_fields === false && $entity->has_parallel_checking && $entity->field_get('updated')) {
        $items['!old_updated']->value_set( # form value ?: storage value
          core::sanitize_datetime($items['!old_updated']->value_request_get()) ?: $form->_instance->updated
        );
      }
    }
  }

  static function on_validate($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
      # prevent parallel update
        if ($entity->has_parallel_checking && $entity->field_get('updated')) {
          $new_updated = core::deep_clone($form->_instance)->select()->updated; # storage value
          $old_updated = $items['!old_updated']->value_get();                   # form    value
          if ($new_updated !== $old_updated) {
            $form->error_set(new text_multiline([
              'While editing this form, someone made changes in parallel and saved them!',
              'Reload this page and make changes again to prevent inconsistency.']));
            return;
          }
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
      # transfer new values to instance
        foreach ($entity->fields as $c_name => $c_field) {
          if (!empty($c_field->managing_is_enabled_on_update) &&
               isset($c_field->managing_control_class)) {
            $c_value = null;
            $c_reflection = new \ReflectionClass($c_field->managing_control_class);
            $c_prefix = $c_reflection->implementsInterface('\\effcore\\control_complex') ? '*' : '#';
            $c_control = $items[$c_prefix.$c_name];
            if     ($c_control instanceof control && $c_control instanceof field_checkbox !== true) $c_value = $c_control->  value_get();
            elseif ($c_control instanceof control && $c_control instanceof field_checkbox === true) $c_value = $c_control->checked_get() ? 1 : 0;
            if (!empty($c_field->managing_control_value_manual_get_if_empty) && empty($c_value)) continue;
            if (!empty($c_field->managing_control_value_manual_get         )                   ) continue;
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
          $items['!old_updated']->value_set(
            $form->_instance->updated
          );
        }
      # ↓↓↓ no break ↓↓↓
      case 'cancel':
        if ($form->is_redirect_enabled) {
          url::go(url::back_url_get() ?: $entity->make_url_for_select_multiple());
        }
        break;
    }
  }

}}