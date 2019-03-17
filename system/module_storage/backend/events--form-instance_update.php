<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    if ($entity) {
      $id_keys   = $entity->real_id_get();
      $id_values = explode('+', $instance_id);
      if (count($id_keys) ==
          count($id_values)) {
        $instance = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($instance->select()) {
          foreach ($entity->fields as $c_name => $c_field) {
            if (isset($c_field->field_class)) {
              $c_form_field = new $c_field->field_class();
              $c_form_field->title = $c_field->title;
              $c_form_field->element_attributes['name'] = $c_name;
              $c_form_field->element_attributes = ($c_field->field_element_attributes ?? []) + $c_form_field->element_attributes;
              $c_form_field->build();
              $c_form_field->value_set($instance->{$c_name});
              $items['fields']->child_insert($c_form_field, $c_name);
            }
          }
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($form, $items) {
    $base        = page::current_get()->args_get('base');
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'update':
        $id_keys   = $entity->real_id_get();
        $id_values = explode('+', $instance_id);
        $instance  = new instance($entity_name, array_combine($id_keys, $id_values));
        if ($instance->select()) {
          foreach ($entity->fields as $c_name => $c_field) {
            if (isset($c_field->field_class) && isset($items['#'.$c_name])) {
              $instance->{$c_name} = $items['#'.$c_name]->value_get();
            }
          }
          if ($instance->update())
               message::insert(new text('Instance of entity "%%_entity_name" with id = "%%_instance_id" was updated.',     ['entity_name' => $entity_name, 'instance_id' => $instance_id]));
          else message::insert(new text('Instance of entity "%%_entity_name" with id = "%%_instance_id" was not updated!', ['entity_name' => $entity_name, 'instance_id' => $instance_id]), 'error');
        }
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: $base.'/select/'.$entity_name);
        break;
    }
  }

}}