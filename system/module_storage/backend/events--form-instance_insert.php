<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\markup;
          use \effcore\page;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_instance_insert {

  static function on_init($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    if ($entity) {
      if ($entity->managing_is_on) {
        $has_enabled_fields = false;
        foreach ($entity->fields as $c_name => $c_field) {
          if (!empty($c_field->field_can_insert) && isset($c_field->field_class)) {
            $c_form_field = new $c_field->field_class;
            $c_form_field->title = $c_field->title;
            $c_form_field->element_attributes['name'] = $c_name;
            $c_form_field->element_attributes = ($c_field->field_element_attributes ?? []) + $c_form_field->element_attributes;
            foreach ($c_field->field_properties ?? [] as $c_prop_name => $c_prop_value) $c_form_field->{$c_prop_name} = $c_prop_value;
            $c_form_field->form_current_set($form);
            $c_form_field->build();
            $items['fields']->child_insert($c_form_field, $c_name);
            if ($c_form_field->disabled_get() == false) {
              $has_enabled_fields = true;
            }
          }
        }
        if ($items['fields']->children_select_count() == 0 || $has_enabled_fields == false) $items['~insert']->disabled_set();
        if ($items['fields']->children_select_count() == 0) {
          $form->child_update(
            'fields', new markup('x-no-result', [], 'no fields')
          );
        }
      } else core::send_header_and_exit('page_not_found');
    }   else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($form, $items) {
    $base        = page::get_current()->args_get('base');
    $entity_name = page::get_current()->args_get('entity_name');
    $entity = entity::get($entity_name);
    switch ($form->clicked_button->value_get()) {
      case 'insert':
      # @todo: make functionality
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/manage/instances/select/'.core::sanitize_id($entity->group).'/'.$entity->name);
        break;
    }
  }

}}