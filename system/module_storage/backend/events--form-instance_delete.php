<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\entity;
use effcore\instance;
use effcore\markup;
use effcore\message;
use effcore\page;
use effcore\text;
use effcore\url;

abstract class events_form_instance_delete {

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
                            if (empty($form->_instance->is_embedded)) {
                                $form->attribute_insert('data-entity_name', $form->entity_name);
                                $form->attribute_insert('data-instance_id', $form->instance_id);
                                $question = new markup('p', [], new text('Delete item of type "%%_type" with ID = "%%_id"?', ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id]));
                                $form->child_select('info')->child_insert($question, 'question');
                            } else {$form->child_select('info')->child_insert(new markup('p', [], new text('entity is embedded'                         )), 'message_error'); $form->has_error_on_build = true;}
                        }     else {$form->child_select('info')->child_insert(new markup('p', [], new text('wrong instance key'                         )), 'message_error'); $form->has_error_on_build = true;}
                    }         else {$form->child_select('info')->child_insert(new markup('p', [], new text('wrong number of instance keys'              )), 'message_error'); $form->has_error_on_build = true;}
                }             else {$form->child_select('info')->child_insert(new markup('p', [], new text('management for this entity is not available')), 'message_error'); $form->has_error_on_build = true;}
            }                 else {$form->child_select('info')->child_insert(new markup('p', [], new text('wrong entity name'                          )), 'message_error'); $form->has_error_on_build = true;}
        }                     else {$form->child_select('info')->child_insert(new markup('p', [], new text('wrong management group'                     )), 'message_error'); $form->has_error_on_build = true;}
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false) {
            if (isset($items['~delete'])) $items['~delete']->disabled_set(false);
            if (isset($items['~cancel'])) $items['~cancel']->disabled_set(false);
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'delete':
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'delete':
                $form->_result = $form->_instance->delete();
                if ($form->is_show_result_message && $form->_result !== null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was deleted.',     ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id])         );
                if ($form->is_show_result_message && $form->_result === null) message::insert(new text('Item of type "%%_type" with ID = "%%_id" was not deleted!', ['type' => (new text($entity->title))->render(), 'id' => $form->instance_id]), 'error');
                # ↓↓↓ no break ↓↓↓
            case 'cancel':
                if (empty(page::get_current()->args_get('back_delete_is_canceled'))) {
                    url::go(url::back_url_get() ?: $entity->make_url_for_select_multiple());
                }
                break;
        }
    }

}
