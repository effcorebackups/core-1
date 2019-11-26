<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
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
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $instance_id = page::get_current()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->id_get_real();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $form->_instance = new instance($entity->name, array_combine($id_keys, $id_values));
        if ($form->_instance->select()) {
        # fixation of 'updated' value for prevent parallel update (not secure: only for organizational methods)
          if ($entity->has_parallel_checking && $entity->ws_updated) {
            $hidden_updated = new field_hidden('updated');
            $hidden_updated->value_set(core::sanitize_datetime($hidden_updated->value_request_get()) ?: $form->_instance->updated);
            $form->child_insert($hidden_updated, 'hidden_updated');
          }
        # make fields for managing
          $has_enabled_fields = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_update_is_enabled) && isset($c_field->managing_field_class)) {
              $c_form_field = new $c_field->managing_field_class;
              $c_form_field->title = $c_field->title;
              $c_form_field->element_attributes['name'] = $c_name;
              $c_form_field->element_attributes = ($c_field->managing_field_element_attributes           ?? []) + $c_form_field->element_attributes;
              $c_form_field->element_attributes = ($c_field->managing_field_element_attributes_on_update ?? []) + $c_form_field->element_attributes;
              foreach ($c_field->managing_field_properties           ?? [] as $c_prop_name => $c_prop_value) $c_form_field->{$c_prop_name} = $c_prop_value;
              foreach ($c_field->managing_field_properties_on_update ?? [] as $c_prop_name => $c_prop_value) $c_form_field->{$c_prop_name} = $c_prop_value;
              $c_form_field->form_current_set($form);
              $c_form_field->entity_name = $entity->name;
              $c_form_field->entity_field_name = $c_name;
              $c_form_field->build();
              $c_form_field->value_set_initial($form->_instance->{$c_name}, true);
              if (empty($c_field->managing_field_value_manual_set) && $c_form_field instanceof field_checkbox == true) $c_form_field->checked_set($form->_instance->{$c_name});
              if (empty($c_field->managing_field_value_manual_set) && $c_form_field instanceof field_checkbox != true) $c_form_field->value_set  ($form->_instance->{$c_name});
              $items['fields']->child_insert($c_form_field, $c_name);
              if ($c_form_field->disabled_get() == false) {
                $has_enabled_fields = true;
              }
            }
          }
          if ($items['fields']->children_select_count() == 0 || $has_enabled_fields == false) $items['~update']->disabled_set();
          if ($items['fields']->children_select_count() == 0) {
            $form->child_update(
              'fields', new markup('x-no-result', [], 'no fields')
            );
          }
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_validate($event, $form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if (!empty($form->_instance)) {
          # prevent parallel update
            if ($entity->has_parallel_checking && $entity->ws_updated) {
              $hidden_updated = $form->child_select('hidden_updated');
              $hidden_updated->value_get();
              $fresh_instance = clone $form->_instance;
              $fresh_instance->select();
              if ($fresh_instance->updated != $hidden_updated->value_get()) {
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
    $back_update_0 = page::get_current()->args_get('back_update_0');
    $back_update_n = page::get_current()->args_get('back_update_n');
    $back_return_0 = page::get_current()->args_get('back_return_0');
    $back_return_n = page::get_current()->args_get('back_return_n');
    $entity_name   = page::get_current()->args_get('entity_name'  );
    $entity = entity::get($entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'update':
          if (!empty($form->_instance)) {
          # transfer new values to instance
            foreach ($entity->fields as $c_name => $c_field) {
              if (isset($c_field->managing_field_class) && isset($items['#'.$c_name])) {
                if (!empty($c_field->managing_field_value_manual_get_if_empty) && $items['#'.$c_name]->value_get() == '') continue;
                if (!empty($c_field->managing_field_value_manual_get         )                                          ) continue;
                if ($items['#'.$c_name] instanceof field_checkbox == true) $form->_instance->{$c_name} = $items['#'.$c_name]->checked_get() ? 1 : 0;
                if ($items['#'.$c_name] instanceof field_checkbox != true) $form->_instance->{$c_name} = $items['#'.$c_name]->value_get  ();
              }
            }
          # update values
            if ($form->_instance->update())
                 message::insert(new text('Item of type "%%_name" with id = "%%_id" was updated.',     ['name' => translation::get($entity->title), 'id' => implode('+', $form->_instance->values_id_get()) ])           );
            else message::insert(new text('Item of type "%%_name" with id = "%%_id" was not updated!', ['name' => translation::get($entity->title), 'id' => implode('+', $form->_instance->values_id_get()) ]), 'warning');}
          url::go($back_update_0 ?: (url::back_url_get() ?: (
                  $back_update_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          break;
      }
    }
  }

}}