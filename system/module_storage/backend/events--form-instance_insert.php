<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\core;
          use \effcore\entity;
          use \effcore\field_checkbox;
          use \effcore\instance;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\text;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_instance_insert {

  static function on_init($event, $form, $items) {
    if (!isset($form->managing_group_id)) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!isset($form->entity_name      )) $form->entity_name       = page::get_current()->args_get('entity_name');
    $entity = entity::get($form->entity_name);
    $groups = entity::groups_managing_get();
    if ($entity) {
      if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
        $form->attribute_insert('data-entity_name', $form->entity_name);
        $form->_instance = new instance($entity->name);
        if ($entity->managing_is_enabled) {
          $has_controls = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_insert_is_enabled) && isset($c_field->managing_control_class)) {
              $c_control = new $c_field->managing_control_class;
              $c_control->title = $c_field->title;
              $c_control->element_attributes['name'] = $c_name;
              $c_control->element_attributes = ($c_field->managing_control_element_attributes           ?? []) + $c_control->element_attributes;
              $c_control->element_attributes = ($c_field->managing_control_element_attributes_on_insert ?? []) + $c_control->element_attributes;
              foreach ($c_field->managing_control_properties           ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
              foreach ($c_field->managing_control_properties_on_insert ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
              $c_control->form_current_set($form);
              $c_control->entity_name = $entity->name;
              $c_control->entity_field_name = $c_name;
              $c_control->build();
              $c_control->value_set_initial('', true);
              $items['fields']->child_insert($c_control, $c_name);
              if ($c_control->disabled_get() == false) {
                $has_controls = true;
              }
            }
          }
          if ($items['fields']->children_select_count() == 0 || $has_controls == false) $items['~insert']->disabled_set();
          if ($items['fields']->children_select_count() == 0) {
            $form->child_update(
              'fields', new markup('x-no-result', [], 'no fields')
            );
          }
        } else core::send_header_and_exit('page_not_found');
      }   else core::send_header_and_exit('page_not_found');
    }     else core::send_header_and_exit('page_not_found');
  }

  static function on_submit($event, $form, $items) {
    $entity = entity::get($form->entity_name);
    if ($entity) {
      switch ($form->clicked_button->value_get()) {
        case 'insert':
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_insert_is_enabled) && isset($c_field->managing_control_class)) {
              $c_reflection = new \ReflectionClass($c_field->managing_control_class);
              $c_prefix = $c_reflection->implementsInterface('\\effcore\\complex_control') ? '*' : '#';
              if (!empty($c_field->managing_control_value_manual_get_if_empty) && $items[$c_prefix.$c_name]->value_get() == '') continue;
              if (!empty($c_field->managing_control_value_manual_get         )                                                ) continue;
              if ($items[$c_prefix.$c_name] instanceof field_checkbox == true) $form->_instance->{$c_name} = $items[$c_prefix.$c_name]->checked_get() ? 1 : 0;
              if ($items[$c_prefix.$c_name] instanceof field_checkbox != true) $form->_instance->{$c_name} = $items[$c_prefix.$c_name]->value_get  ();
            }
          }
        # insert action
          $form->_result_insert = $form->_instance->insert();
        # show messages
          if ($form->_result_insert)
               message::insert(new text('Item of type "%%_type" with ID = "%%_id" was inserted.',     ['type' => translation::get($entity->title), 'id' => implode('+', $form->_instance->values_id_get()) ])           );
          else message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not inserted!', ['type' => translation::get($entity->title), 'id' => 'n/a'                                           ]), 'warning');
        # ↓↓↓ no break ↓↓↓
        case 'cancel':
        # going back
          if (empty(page::get_current()->args_get('back_insert_is_canceled'))) {
            $back_insert_0 = page::get_current()->args_get('back_insert_0');
            $back_insert_n = page::get_current()->args_get('back_insert_n');
            url::go($back_insert_0 ?: (url::back_url_get() ?: (
                    $back_insert_n ?: '/manage/data/'.$entity->group_managing_get_id().'/'.$entity->name)));
          }
          break;
      }
    }
  }

}}