<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\complex_control;
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
    if (!$form->managing_group_id) $form->managing_group_id = page::get_current()->args_get('managing_group_id');
    if (!$form->entity_name      ) $form->entity_name       = page::get_current()->args_get('entity_name');
    $entity = entity::get($form->entity_name);
    $groups = entity::get_managing_group_ids();
    if ($entity) {
      if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
        $form->attribute_insert('data-entity_name', $form->entity_name);
        $form->_instance = new instance($entity->name);
        if ($entity->managing_is_enabled) {
          $has_controls = false;
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_insert_is_enabled) &&
                 isset($c_field->managing_control_class)) {
              $c_control = new $c_field->managing_control_class;
              $c_control->title = $c_field->title;
              $c_control->element_attributes['name'] = $c_name;
              $c_control->element_attributes = ($c_field->managing_control_element_attributes           ?? []) + $c_control->element_attributes;
              $c_control->element_attributes = ($c_field->managing_control_element_attributes_on_insert ?? []) + $c_control->element_attributes;
              foreach ($c_field->managing_control_properties           ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
              foreach ($c_field->managing_control_properties_on_insert ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
              $c_control->cform = $form;
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
              'fields', new markup('x-no-items', [], 'no fields')
            );
          }
          if (empty($entity->has_button_insert_and_update)) {
            $form->child_delete('button_insert_and_update');
          }
          if (empty($entity->has_message_for_additional_controls) == false) {
            $form->child_select('fields')->child_insert(
              new markup('x-form-message', [], ['message' => new text(
                'Additional controls will become available after insertion (in update mode).')
              ], -20), 'form_message'
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
        case 'insert_and_update':
          foreach ($entity->fields as $c_name => $c_field) {
            if (!empty($c_field->managing_on_insert_is_enabled) &&
                 isset($c_field->managing_control_class)) {
              $c_value = null;
              $c_reflection = new \ReflectionClass($c_field->managing_control_class);
              $c_prefix = $c_reflection->implementsInterface('\\effcore\\complex_control') ? '*' : '#';
              if      ($items[$c_prefix.$c_name] instanceof complex_control       ) $c_value = $items[$c_prefix.$c_name]->value_get_complex();
              else if ($items[$c_prefix.$c_name] instanceof field_checkbox != true) $c_value = $items[$c_prefix.$c_name]->value_get        ();
              else if ($items[$c_prefix.$c_name] instanceof field_checkbox == true) $c_value = $items[$c_prefix.$c_name]->checked_get      () ? 1 : 0;
              if (!empty($c_field->managing_control_value_manual_get_if_empty) && $c_value == '') continue;
              if (!empty($c_field->managing_control_value_manual_get         )                  ) continue;
              $form->_instance->{$c_name} = $c_value;
            }
          }
        # insert action
          $form->_result = $form->_instance->insert();
        # show messages
          if ($form->is_show_result_message && $form->_result != null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was inserted.',     ['type' => translation::get($entity->title), 'id' => implode('+', $form->_instance->values_id_get()) ])           );
          if ($form->is_show_result_message && $form->_result == null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not inserted!', ['type' => translation::get($entity->title), 'id' => 'n/a'                                           ]), 'warning');
        # ↓↓↓ no break ↓↓↓
        case 'cancel':
        # going back
          if ($form->is_redirect_enabled) {
            $back_insert_0 = page::get_current()->args_get('back_insert_0');
            $back_insert_n = page::get_current()->args_get('back_insert_n');
            if (!empty($entity->has_button_insert_and_update)) # when click 'insert and update'
              if ($form->clicked_button->value_get() == 'insert_and_update')
                if ($form->_result instanceof instance)
                  url::go($back_insert_0 ?:
                         ($back_insert_n ?: $form->_result->make_url_for_update().(url::back_url_get() ? '?'.url::back_part_make_custom(url::back_url_get()) : '') ));
            url::go($back_insert_0 ?: (url::back_url_get() ?: (
                    $back_insert_n ?: $entity->make_url_for_select_multiple() )));
          }
          break;
      }
    }
  }

}}