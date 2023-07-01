<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\Control;
use effcore\Entity;
use effcore\Field_Checkbox;
use effcore\Instance;
use effcore\Markup;
use effcore\Message;
use effcore\Page;
use effcore\Text;
use effcore\Url;
use ReflectionClass;

abstract class Events_Form_Instance_insert {

    static function on_build($event, $form) {
        if (!$form->managing_group_id) $form->managing_group_id = Page::get_current()->args_get('managing_group_id');
        if (!$form->entity_name      ) $form->entity_name       = Page::get_current()->args_get('entity_name');
        $entity = Entity::get($form->entity_name);
        $groups = Entity::get_managing_group_ids();
        if ($form->managing_group_id === null || isset($groups[$form->managing_group_id])) {
            if ($entity) {
                if ($entity->managing_is_enabled) {
                    $form->attribute_insert('data-entity_name', $form->entity_name);
                    $form->_instance = new Instance($entity->name);
                    $form->child_select('fields')->children_delete();
                    foreach ($entity->fields as $c_name => $c_field) {
                        if (!empty($c_field->managing_is_enabled_on_insert) &&
                             isset($c_field->managing_control_class)) {
                            $c_control = new $c_field->managing_control_class;
                            $c_control->cform = $form;
                            $c_control->title = $c_field->title;
                            $c_control->element_attributes['name'] = $c_name;
                            $c_control->element_attributes = ($c_field->managing_control_element_attributes           ?? []) + $c_control->element_attributes;
                            $c_control->element_attributes = ($c_field->managing_control_element_attributes_on_insert ?? []) + $c_control->element_attributes;
                            if (isset($c_field->managing_control_properties          ) && is_array($c_field->managing_control_properties          )) foreach ($c_field->managing_control_properties           as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                            if (isset($c_field->managing_control_properties_on_insert) && is_array($c_field->managing_control_properties_on_insert)) foreach ($c_field->managing_control_properties_on_insert as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
                            $c_control->entity_name = $entity->name;
                            $c_control->entity_field_name = $c_name;
                            $c_control->value_set_initial('', true);
                            $form->child_select('fields')->child_insert($c_control, $c_name);
                        }
                    }
                    if (empty($entity->has_button_insert_and_update)) {
                        $form->child_delete('button_insert_and_update');
                    }
                    # form messages
                    if ($form->child_select('fields')->children_select_count() === 0) {
                        $form->child_select('fields')->child_insert(new Markup('x-no-items', [], 'No fields.'), 'message_no_fields');
                        $form->has_no_fields = true;
                    }
                    if ($form->has_no_fields === false && empty($entity->has_message_for_additional_controls) === false) {
                        $form->child_select('fields')->child_insert(
                            new Markup('x-form-message', [], ['message' => new Text(
                                'Additional controls will become available after insertion (in update mode).')
                            ], -500), 'message_additional_controls'
                        );
                    }
                } else {$form->child_select('fields')->child_insert(new Markup('p', [], new Text('management for this entity is not available')), 'message_error'); $form->has_error_on_build = true;}
            }     else {$form->child_select('fields')->child_insert(new Markup('p', [], new Text('wrong entity name'                          )), 'message_error'); $form->has_error_on_build = true;}
        }         else {$form->child_select('fields')->child_insert(new Markup('p', [], new Text('wrong management group'                     )), 'message_error'); $form->has_error_on_build = true;}
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            if (isset($items['~insert'           ])) $items['~insert'           ]->disabled_set(false);
            if (isset($items['~insert_and_update'])) $items['~insert_and_update']->disabled_set(false);
            if (isset($items['~cancel'           ])) $items['~cancel'           ]->disabled_set(false);
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                # preliminary definition of instance ID
                if (!$form->has_error()) {
                    foreach ($entity->id_get() as $c_name) {
                        if (isset($items['#'.$c_name])) {
                            $c_value = $items['#'.$c_name]->value_get();
                            if ($c_value) {
                                $form->_instance->{$c_name} = $c_value;
                            } else return;
                            } else return; }
                    $form->instance_id = implode('+', $form->_instance->values_id_get());
                    Page::get_current()->args_set('instance_id', $form->instance_id);
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                foreach ($entity->fields as $c_name => $c_field) {
                    if (!empty($c_field->managing_is_enabled_on_insert) &&
                         isset($c_field->managing_control_class)) {
                        $c_value = null;
                        $c_reflection = new ReflectionClass($c_field->managing_control_class);
                        $c_prefix = $c_reflection->implementsInterface('\\effcore\\Control_complex') ? '*' : '#';
                        $c_control = $items[$c_prefix.$c_name];
                        if     (is_object($c_control) && $c_control instanceof Control && $c_control instanceof Field_Checkbox !== true) $c_value = $c_control->  value_get();
                        elseif (is_object($c_control) && $c_control instanceof Control && $c_control instanceof Field_Checkbox === true) $c_value = $c_control->checked_get() ? 1 : 0;
                        if (!empty($c_field->managing_control_value_manual_get_if_empty) && empty($c_value)) continue;
                        if (!empty($c_field->managing_control_value_manual_get         )                   ) continue;
                        $form->_instance->{$c_name} = $c_value;
                    }
                }
                # insert action
                $form->_result = $form->_instance->insert();
                # show messages
                if ($form->is_show_result_message && $form->_result !== null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was inserted.',     ['type' => (new Text($entity->title))->render(), 'id' => implode('+', $form->_instance->values_id_get()) ])           );
                if ($form->is_show_result_message && $form->_result === null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was not inserted!', ['type' => (new Text($entity->title))->render(), 'id' => 'n/a'                                           ]), 'warning');
                # ↓↓↓ no break ↓↓↓
            case 'cancel':
                if ($form->is_redirect_enabled) {
                    if ($form->clicked_button->value_get() === 'insert') Url::go(Url::back_url_get() ?: $entity->make_url_for_select_multiple());
                    if ($form->clicked_button->value_get() === 'cancel') Url::go(Url::back_url_get() ?: $entity->make_url_for_select_multiple());
                    if ($form->clicked_button->value_get() === 'insert_and_update') {
                        if ($form->_result instanceof Instance) {
                            Url::go($form->_result->make_url_for_update().'?'.Url::back_part_make('back', $entity->make_url_for_select_multiple()));
                        }
                    }
                }
                break;
        }
    }

}
