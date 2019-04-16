<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\translation;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($form, &$items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $form->_instance = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($form->_instance->select()) {
          $has_enabled_fields = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (isset($c_field->field_class)) {
              $c_form_field = new $c_field->field_class();
              $c_form_field->title = $c_field->title;
              $c_form_field->element_attributes['name'] = $c_name;
              $c_form_field->element_attributes = ($c_field->field_element_attributes ?? []) + $c_form_field->element_attributes;
              foreach ($c_field->field_properties ?? [] as $c_prop_name => $c_prop_value) $c_form_field->{$c_prop_name} = $c_prop_value;
              $c_form_field->cform_set($form);
              $c_form_field->build();
              if (empty($c_field->field_value_not_select)) {
                $c_form_field->value_set(
                  $form->_instance->{$c_name}
                );
              }
              $items['fields']->child_insert($c_form_field, $c_name);
              if ($c_form_field->disabled_get() == false) {
                $has_enabled_fields = true;
              }
            }
          }
          if ($items['fields']->children_select_count() == 0 || $has_enabled_fields == false) $items['~update']->disabled_set();
          if ($items['fields']->children_select_count() == 0) {
            $form->child_update(
              'fields', new markup('x-no-result', [], 'no editable fields')
            );
          }
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($form, $items) {
    $back_update = page::current_get()->args_get('back_update');
    $back_cancel = page::current_get()->args_get('back_cancel');
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        if (!empty($form->_instance)) {
          foreach ($entity->fields as $c_name => $c_field) {
            if (isset($c_field->field_class) && isset($items['#'.$c_name])) {
              if (!empty($c_field->field_value_insert_null_if_empty) && $items['#'.$c_name]->value_get() == '') {$form->_instance->{$c_name} = null; continue;}
              if (!empty($c_field->field_value_not_insert_if_empty ) && $items['#'.$c_name]->value_get() == '') {                                    continue;}
              if (!empty($c_field->field_value_not_insert          )                                          ) {                                    continue;}
              $form->_instance->{$c_name} = $items['#'.$c_name]->value_get();
            }
          }
          if ($form->_instance->update())
               message::insert_to_storage(new text('%%_name with id = "%%_id" was updated.',     ['name' => translation::get($entity->title), 'id' => $instance_id]));
          else message::insert_to_storage(new text('%%_name with id = "%%_id" was not updated!', ['name' => translation::get($entity->title), 'id' => $instance_id]), 'warning');
        }
                     url::go(url::back_url_get() ?: ($back_update ?: '/manage/instances/select/'.core::sanitize_id($entity->group).'/'.$entity->name)); break;
      case 'cancel': url::go(url::back_url_get() ?: ($back_cancel ?: '/manage/instances/select/'.core::sanitize_id($entity->group).'/'.$entity->name)); break;
    }
  }

}}