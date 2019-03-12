<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\manage_instances;
          use \effcore\page;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_update {

  static function on_init($form, $items) {
    manage_instances::instance_update(page::current_get(), true); # emulation for access checking
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
          foreach ($entity->fields as $c_id => $c_field) {
            if (isset($c_field->field_class)) {
              $c_form_field = new $c_field->field_class();
              $c_form_field->build();
              $c_form_field->title = $c_field->title;
              $c_form_field->value_set($instance->{$c_id});
              $items['fields']->child_insert($c_form_field, $c_id);
            }
          }
        }
      }
    }
  }

  static function on_submit($form, $items) {
    $base        = page::current_get()->args_get('base');
    $entity_name = page::current_get()->args_get('entity_name');
    $instance_id = page::current_get()->args_get('instance_id');
    switch ($form->clicked_button->value_get()) {
      case 'update':
      # @todo: make functionality
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: $base.'/select/'.$entity_name);
        break;
    }
  }

}}